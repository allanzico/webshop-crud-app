<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
       return $this->show($entityManager);
    }


    public function show(EntityManagerInterface $entityManager){

        $repository = $entityManager->getRepository(Product::class);
        $products = $repository->findAllByNewest();

        return $this->render('product/index.html.twig',[
            'products' =>$products,

        ]);

    }

    /**
     * @Route("/product/new", name="new_product")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */

    //Add new product to the database
    public function new(EntityManagerInterface $entityManager, Request $request){

        //Create and Render form
        $form = $this->createForm(\ProductFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
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
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager){

        //Return Edit form
        $form = $this->createForm(\ProductFormType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $product->setUpdatedAt();
            $entityManager->persist($product);
            $entityManager->flush();


            return  $this->redirectToRoute('homepage',['id'=>$product->getId()]);

    }
        return  $this->render('product/edit.html.twig' ,[
            'productForm' => $form->createView(),
        ]);
    }

}
