<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repository,Request $request, PaginatorInterface $paginator): Response
    {
        $categories = $repository->createQueryBuilder('category')->getQuery();
        $pagination = $paginator->paginate($categories,$request->query->getInt('page','1'),5);
        return $this->render('admin/category/index.html.twig', [
            'categories' => $pagination
        ]);
    }

    #[Route('/category/add',name: 'app_category_add')]
    public function add(CategoryRepository $categoryRepository , Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $categoryRepository->add($category,true);
            return $this->redirectToRoute('app_category');
        }

        return $this->render('admin/category/add.html.twig',['CategoryForm' => $form->createView()]);
    }

    #[Route('/category/edit/{id}',name: 'app_category_edit')]
    public function edit(CategoryRepository $categoryRepository , Request $request): Response
    {
        $category = $categoryRepository->find($request->get('id'));
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $categoryRepository->add($category,true);
            return $this->redirectToRoute('app_category');
        }

        return $this->render('admin/category/edit.html.twig',['CategoryForm' => $form->createView()]);
    }

    #[Route('/category/delete/{id}',name: 'app_category_delete')]
    public function delete(CategoryRepository $categoryRepository , Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry): Response
    {
        $category = $categoryRepository->find($request->get('id'));
        $categoryRepository->remove($category);
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('app_category');
    }
}
