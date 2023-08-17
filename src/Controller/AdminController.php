<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Lustre;
use App\Entity\Mineral;
use App\Entity\Category;
use App\Form\AddColorType;
use App\Form\CategoryType;
use App\Form\AddLustreType;
use App\Repository\ColorRepository;
use App\Repository\LustreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/color', name: 'color')]
    public function colorslist(ColorRepository $colorRepository, Request $request): Response
    {
        return $this->render('admin/color/colors_list.html.twig', [
            'colors' => $colorRepository->findPaginateColors($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/color/new', name: 'new_color')]
    public function new_color(Request $request, EntityManagerInterface $entityManager): Response
    {
        $color = new Color();

        $form = $this->createForm(AddColorType::class, $color);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $color = $form->getData();

            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('color');
        }

        return $this->render('admin/color/new_color.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/color/{slug}/edit', name: 'edit_color')]
    public function edit_color(Color $color, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddColorType::class, $color);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $color = $form->getData();

            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('color');
        }

        return $this->render('admin/color/edit_color.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/color/{slug}/delete', name: 'delete_color')]
    public function deleteColor(Color $color, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($color);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('color');
    }

    #[Route('/lustre', name: 'lustre')]
    public function lustresList(LustreRepository $lustreRepository, Request $request): Response
    {
        return $this->render('admin/lustre/lustres_list.html.twig', [
            'lustres' => $lustreRepository->findPaginateLustres($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/lustre/new', name: 'new_lustre')]
    public function new_lustre(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lustre = new Lustre();

        $form = $this->createForm(AddLustreType::class, $lustre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lustre = $form->getData();

            $entityManager->persist($lustre);
            $entityManager->flush();

            return $this->redirectToRoute('lustre');
        }

        return $this->render('admin/lustre/new_lustre.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/lustre/{slug}/edit', name: 'edit_lustre')]
    public function edit_lustre(Lustre $lustre, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddLustreType::class, $lustre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lustre = $form->getData();

            $entityManager->persist($lustre);
            $entityManager->flush();

            return $this->redirectToRoute('lustre');
        }

        return $this->render('admin/lustre/edit_lustre.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/lustre/{slug}/delete', name: 'delete_lustre')]
    public function deleteLustre(Lustre $lustre, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($lustre);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('lustre');
    }

    #[Route('/mineral/{slug}/delete', name: 'delete_mineral')]
    public function deleteMineral(Mineral $mineral, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($mineral);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_mineral');
    }

    #[Route('/category/new', name: 'new_category')]
    public function new_category(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('admin/new_category.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/category/{slug}/editCategory', name: 'edit_category')]
    public function edit_category(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('admin/edit_category.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/category/{slug}/delete', name: 'delete_category')]
    public function deletecategory(Category $category, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($category);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_category');
    }

}
