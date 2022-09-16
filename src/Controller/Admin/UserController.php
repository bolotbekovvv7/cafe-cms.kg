<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {

        return $this->render(view: 'admin/index.html.twig', parameters: []);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render(view: 'admin/users.html.twig', parameters: ['users' => $users]);
    }


    #[Route('/users/add/{id}', name: 'app_admin_add')]
    public function add(UserRepository $userRepository, Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry): Response
    {
        $user = $userRepository->find($request->get('id'));
        $user->setRoles(['ROLE_ADMIN']);
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/lower/{id}', name: 'app_admin_lower')]
    public function lower(UserRepository $userRepository, Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry): Response
    {
        $user = $userRepository->find($request->get('id'));
        $user->setRoles(['ROLE_USER']);
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/delete/{id}', name: 'app_user_delete')]
    public function delete(UserRepository $userRepository, Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry): Response
    {
        $user = $userRepository->find($request->get('id'));
        $userRepository->remove($user);
        $managerRegistry->getManager()->flush();
        return $this->redirectToRoute('app_admin_users');
    }


}
