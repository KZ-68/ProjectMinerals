<?php

namespace App\Controller;

use App\Entity\Mineral;
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

    #[Route('/admin/mineral/{id}/delete', name: 'delete_mineral')]
    public function deleteMineral(Mineral $mineral, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($mineral);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_mineral');
    }
}
