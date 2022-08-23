<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Persistence\Event\ManagerEventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render(view: 'admin/index.html.twig', parameters: [
            'users' => $users
        ]);
    }

    #[Route('/admin_add/{id}', name: 'app_admin_add')]
    public function add(UserRepository $userRepository, Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry): Response
    {
        $user = $userRepository->find($request->get('id'));
        $user->setRoles(['ROLE_ADMIN']);
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('app_admin');
    }

}
