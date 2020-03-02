<?php

namespace App\Controller;

use App\Entity\Category;
use App\Forms\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @IsGranted("ROLE_ADMIN")
 */


class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category", name="category")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager)
    {

        $repository = $entityManager->getRepository(Category::class);
        $categories = $repository->findAllByNewest();

        return $this->render('category/index.html.twig',[
            'categories' =>$categories,

        ]);
    }


    /**
     * @Route("/admin/category/new", name="new_category")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse|Response
     */
    //Add new category to the database
    public function new(EntityManagerInterface $entityManager, Request $request){

        //Create and Render form
        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $category = $form->getData();
            $category->setCreatedAt();
            $category->setUpdatedAt();
            $entityManager->persist($category);
            $entityManager->flush();


            return  $this->redirectToRoute('category');

        }

        return $this->render('category/new.html.twig',
            ['categoryForm'=>$form->createView()]);
    }

    /**
     * @Route("/admin/category/{id}edit", name="edit_category")
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager){

        //Return Edit form
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $category = $form->getData();
            $category->setUpdatedAt();
            $entityManager->persist($category);
            $entityManager->flush();


            return  $this->redirectToRoute('category',['id'=>$category->getId()]);

        }
        return  $this->render('category/edit.html.twig' ,[
            'categoryForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/category/{id}", name="delete_category")
     * @param $id
     * @return RedirectResponse
     */

    public function delete($id){

        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
        $entityManager->remove($category);
        $entityManager->flush();

        return  $this->redirectToRoute('category',['id'=>$category->getId()]);

    }
}
