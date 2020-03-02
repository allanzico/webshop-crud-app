<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;

class ProductController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="homepage", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function index(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {

        $repository = $entityManager->getRepository(Product::class);
        $products = $repository->findAllByNewest();

        $response = [];
        foreach ($products as $product) {
            $response[] = $product->toArray();
        }

//        dd($response);
//        dd($serializer->serialize($products, 'json'));
//        dd(new JsonResponse($products));


        //return new JsonResponse($response);
        return $this->render('product/index.html.twig',[
            'products' =>$response,

        ]);
    }

    /**
     * @Route("/product/show", name="show_product" )
     * @param EntityManagerInterface $entityManager
     * @param Product $product
     * @return Response
     */

    public function show(EntityManagerInterface $entityManager, Product $product){
        return $this->render('product/show.html.twig', [
            'product'=> $product,
        ]);
    }

    /**
     * @Route("/product/new", name="new_product", methods={"POST"})
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    //Add new product to the database
    public function new(EntityManagerInterface $entityManager, Request $request, UploaderHelper $uploaderHelper ){

        $data = json_decode($request->getContent(), true);
        $product = new Product();
        $product->setName($data['name'])
            ->setCreatedAt()
            ->setUpdatedAt()
            ->setQuantity($data['quantity'])
            ->setImageFilename($data['image_filename'])
            ->setCategory($this->entityManager->find(Category::class, $data['category_id']));

        $this->entityManager->persist($product);
        $this->entityManager->flush();

       return new JsonResponse(['text' => 'Successfully added product :)']);


//        //Create and Render form
//        $form = $this->createForm(\ProductFormType::class, $product);
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()){
//            $uploadedFile = $form->get('imageFilename')->getData();
//            if ($uploadedFile){
//                $newFilename = $uploaderHelper->uploadImage($uploadedFile);
//                $product->setImageFilename($newFilename);
//
//            }
//            $product->setCreatedAt();
//            $product->setUpdatedAt();
//            $entityManager->persist($product);
//            $entityManager->flush();


//        }
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
            $entityManager = $this->getDoctrine()->getManager();
            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadImage($uploadedFile);
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
