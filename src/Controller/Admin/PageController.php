<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/page', name: 'app_page')]
    public function index(PageRepository $pageRepository,Request $request, PaginatorInterface $paginator): Response
    {
        $query = $pageRepository->createQueryBuilder('page')->getQuery();
        $pagination = $paginator->paginate($query,$request->query->getInt('page',1),5);

        return $this->render('admin/page/index.html.twig',['pages' => $pagination]);
    }

    #[Route('/page/add', name: 'app_page_add')]
    public function add(PageRepository $pageRepository, Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $page = $form->getData();
            $pageRepository->add($page, true);
            return $this->redirectToRoute('app_page');
        } else {
            echo "Warnings";
        }


        return $this->render('admin/page/create.html.twig', ['PageAdd' => $form->createView()]);
    }

    #[Route('/page/delete/{id}', name: 'app_page_delete')]
    public function delete(PageRepository $repository, Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry): Response
    {
        $page = $repository->find($request->get('id'));
        $repository->remove($page);
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('app_page');

    }

    #[Route('/page/edit/{id}', name: 'app_page_edit')]
    public function edit(PageRepository $pageRepository , Request $request):Response
    {

        $page = $pageRepository->find($request->get('id'));
        $form = $this->createForm(PageType::class,$page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
         $page = $form->getData();
         $pageRepository->add($page,true);
         return $this->redirectToRoute('app_page');
        }
        return $this->render('admin/page/edit.html.twig', ['PageAdd' => $form->createView(), 'pages' => $page]);
    }
}
