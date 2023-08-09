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
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/color', name: 'app_color')]
    public function colorslist(ColorRepository $colorRepository, Request $request): Response
    {
        return $this->render('admin/colors_list.html.twig', [
            'colors' => $colorRepository->findPaginateColors($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/admin/color/new', name: 'new_color')]
    public function new_color(Request $request, EntityManagerInterface $entityManager): Response
    {
        $color = new Color();

        $form = $this->createForm(AddColorType::class, $color);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $color = $form->getData();

            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('app_color');
        }

        return $this->render('admin/new_color.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/color/{slug}/edit', name: 'edit_color')]
    public function edit_color(Color $color, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddColorType::class, $color);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $color = $form->getData();

            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('app_color');
        }

        return $this->render('admin/edit_color.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/color/{slug}/delete', name: 'delete_color')]
    public function deleteColor(Color $color, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($color);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_color');
    }

    #[Route('/admin/lustre', name: 'app_lustre')]
    public function lustresList(LustreRepository $lustreRepository, Request $request): Response
    {
        return $this->render('admin/lustres_list.html.twig', [
            'lustres' => $lustreRepository->findPaginateLustres($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/admin/lustre/new', name: 'new_lustre')]
    public function new_lustre(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lustre = new Lustre();

        $form = $this->createForm(AddLustreType::class, $lustre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lustre = $form->getData();

            $entityManager->persist($lustre);
            $entityManager->flush();

            return $this->redirectToRoute('app_lustre');
        }

        return $this->render('admin/new_lustre.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/lustre/{slug}/edit', name: 'edit_lustre')]
    public function edit_lustre(Lustre $lustre, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddLustreType::class, $lustre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lustre = $form->getData();

            $entityManager->persist($lustre);
            $entityManager->flush();

            return $this->redirectToRoute('app_lustre');
        }

        return $this->render('admin/edit_lustre.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/lustre/{slug}/delete', name: 'delete_lustre')]
    public function deleteLustre(Lustre $lustre, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($lustre);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_lustre');
    }

    #[Route('/admin/mineral/{slug}/delete', name: 'delete_mineral')]
    public function deleteMineral(Mineral $mineral, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($mineral);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_mineral');
    }

    #[Route('/admin/category/new', name: 'new_category')]
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

        return $this->render('wiki/new_category.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/category/{slug}/editCategory', name: 'edit_category')]
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

        return $this->render('wiki/edit_category.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/category/{slug}/delete', name: 'delete_category')]
    public function deletecategory(Category $category, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($category);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_category');
    }

}
