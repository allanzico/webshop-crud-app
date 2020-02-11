<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager)
    {

        $repository = $entityManager->getRepository(Product::class);
        $products = $repository->findAllByNewest();

        return $this->render('product/index.html.twig',[
            'products' =>$products,

        ]);
    }

    /**
     * @Route("/product/show", name="show_product")
     * @param EntityManagerInterface $entityManager
     * @param Product $product
     * @return Response
     */

    public function show(EntityManagerInterface $entityManager, Product $product){
        return $this->render('article/show.html.twig', [
            'product'=> $product,
        ]);
    }

    /**
     * @Route("/product/new", name="new_product")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    //Add new product to the database
    public function new(EntityManagerInterface $entityManager, Request $request, UploaderHelper $uploaderHelper ){

        $product = new Product();



        //Create and Render form
        $form = $this->createForm(\ProductFormType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form->get('imageFilename')->getData();

            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadImage($uploadedFile);

                $entityManager = $this->getDoctrine()->getManager();
                $product->setImageFilename($newFilename);

            }
            $product->setCreatedAt();
            $product->setUpdatedAt();
            $entityManager->persist($product);
            $entityManager->flush();


            return  $this->redirectToRoute('homepage');

        }

        return $this->render('product/new.html.twig',
            ['productForm'=>$form->createView()]);
    }

    /**
     * @Route("/product/{id}edit", name="edit_product")
     * @param Product $product
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper){

        //Return Edit form
        $form = $this->createForm(\ProductFormType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $uploadedFile = $form->get('imageFilename')->getData();
            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadImage($uploadedFile);

                $entityManager = $this->getDoctrine()->getManager();
                $product->setImageFilename($newFilename);
            }

            $product->setUpdatedAt();
            $entityManager->persist($product);
            $entityManager->flush();


            return  $this->redirectToRoute('homepage',['id'=>$product->getId()]);

    }
        return  $this->render('product/edit.html.twig' ,[
            'productForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/product/{id}", name="delete_product")
     * @param $id
     * @return RedirectResponse
     */

    public function delete($id){

            $entityManager = $this->getDoctrine()->getManager();
            $product = $entityManager->getRepository(Product::class)->find($id);
            $entityManager->remove($product);
            $entityManager->flush();

        return  $this->redirectToRoute('homepage',['id'=>$product->getId()]);

    }

}
