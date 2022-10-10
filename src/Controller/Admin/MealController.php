<?php

namespace App\Controller\Admin;

use App\Entity\Meal;
use App\Form\MealAddType;
use App\Repository\MealRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class MealController extends AbstractController
{
    #[Route('/meal', name: 'app_meal')]
    public function index(MealRepository $mealRepository, Request $request, PaginatorInterface $paginator): Response
    {
        if ($request->get('text') != null) {
            $query = $mealRepository->search($request->get('text'));
        } else {
            $query = $mealRepository->createQueryBuilder('m')
                ->getQuery();
        }
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        $meals = $mealRepository->findAll();

        return $this->render('admin/meal/index.html.twig', [
            'meals' => $pagination
        ]);
    }

    #[Route('/meal/create', name: 'app_meal_create')]
    public function create(Request $request, SluggerInterface $slugger, MealRepository $mealRepository, ValidatorInterface $validator): Response
    {
        //------------------------------------------------------------------------------
        $meal = new Meal();
        $form = $this->createForm(MealAddType::class, $meal);
        $form->handleRequest($request);
        //        Форма
        //-------------------------------------------------------------------------------
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('img')->getData();
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
            }
            if (
                $uploadedFile->move('uploads',
                    $newFilename
                )) {
                $meal = $form->getData();
                $meal->setImg($newFilename);
                $mealRepository->add($meal, true);
                return $this->redirectToRoute('app_meal');
            }


        }

        return $this->render('admin/meal/create.html.twig', ['mealAdd' => $form->createView()
        ]);
    }

    #[Route('/meal/delete/{id}', name: 'app_meal_delete')]
    public function delete(MealRepository $mealRepository, Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry): Response
    {

        $meal = $mealRepository->find($request->get('id'));
        if (unlink('uploads/' . $meal->getImg())) {
            $mealRepository->remove($meal);
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('app_meal');
        }
    }

    #[Route('/meal/edit/{id}', name: 'app_meal_edit')]
    public function edit(MealRepository $mealRepository, Request $request, ManagerRegistry $managerRegistry, ValidatorInterface $validator): Response
    {
        $meal = $mealRepository->find($request->get('id'));
        $form = $this->createForm(MealAddType::class, $meal);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $form->get('img')->getData();
                if ($uploadedFile->move('uploads', $uploadedFile->getClientOriginalName())) {
                    unlink('uploads/' . $meal->getImg());
                    $meal->setName($form->get('name')->getData());
                    $meal->setCategory($form->get('category')->getData());
                    $meal->setDescription($form->get('description')->getData());
                    $meal->setImg($uploadedFile->getClientOriginalName());
                    $managerRegistry->getManager()->flush();
                    return $this->redirectToRoute('app_meal');
                }

            }
        }

        return $this->render('admin/meal/edit.html.twig', ['meal' => $meal, 'mealAdd' => $form->createView()]);
    }
}
