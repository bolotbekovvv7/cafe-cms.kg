<?php

namespace App\Controller\Admin;

use App\Entity\Foods;
use App\Repository\FoodsRepository;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodController extends AbstractController
{
    #[Route('/food', name: 'app_food')]
    public function index(FoodsRepository $foodsRepository): Response
    {
        $foods = $foodsRepository->findAll();
        return $this->render('admin/food/index.html.twig', [
            'foods' => $foods
        ]);
    }

    #[Route('/food/create', name: 'app_food_create')]
    public function create(Request $request, FoodsRepository $foodsRepository): Response
    {

        if ($request->getMethod() == Request::METHOD_POST) {

            if ($request->files != null) {
                /** @var UploadedFile file */
                if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name'])) {
                    $foods = new Foods();
                    $foods->setName($request->request->get('name'));
                    $foods->setCategory($request->request->get('category'));
                    $foods->setDescription($request->request->get('text'));
                    $foods->setImg($_FILES['file']['name']);
                    $foodsRepository->add($foods, true);
                    return $this->redirectToRoute('app_food');
                }

            }
        }

        return $this->render('admin/food/create.html.twig', [
        ]);
    }

    #[Route('/food/delete/{id}', name: 'app_food_delete')]
    public function delete(FoodsRepository $foodsRepository, Request $request): Response
    {
        $food = $foodsRepository->find($request->get('id'));
        $foodsRepository->remove($food);
        return $this->redirectToRoute('app_food');
    }

    #[Route('/food/edit/{id}', name: 'app_food_delete')]
    public function edit(FoodsRepository $foodsRepository, Request $request): Response
    {
        $food = $foodsRepository->find($request->get('id'));
        if ($request->getMethod() == Request::METHOD_POST) {
            if ($request->files != null) {
                /** @var UploadedFile file */
                if (move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name'])) {

                    $food->setName($request->request->get('name'));
                    $food->setCategory($request->request->get('category'));
                    $food->setDescription($request->request->get('text'));
                    $food->setImg($_FILES['file']['name']);
                    return $this->redirectToRoute('app_food');
                }

            }
        }

//        return $this->redirectToRoute('app_food');
        return $this->render('admin/food/edit.html.twig', ['food' => $food]);
    }

}
