<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index_index")
     * @return Response
     */
    public function index(): Response
    {

        $user = $this->getUser();
        return $this->render('index/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/create", name="index_create")
     * @return Response
     */
    public function create(): Response
    {
        return $this->render('index/create.html.twig');
    }




}