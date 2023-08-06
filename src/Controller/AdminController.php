<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Lustre;
use App\Entity\Mineral;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/mineral/{slug}/delete', name: 'delete_mineral')]
    public function deleteMineral(Mineral $mineral, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($mineral);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_mineral');
    }

    #[Route('/admin/category/{slug}/delete', name: 'delete_category')]
    public function deletecategory(Category $category, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($category);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_category');
    }

    #[Route('/admin/color/{slug}/delete', name: 'delete_color')]
    public function deleteColor(Color $color, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($color);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_color');
    }

    #[Route('/admin/lustre/{slug}/delete', name: 'delete_lustre')]
    public function deleteLustre(Lustre $lustre, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($lustre);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_lustre');
    }
}
