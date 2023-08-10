<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur courant
        $user = $this->getUser();
        // Crée le formulaire par le biais du FormType associé
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $avatarFile */
            // Récupère les données de 'avatar' venant du formulaire
            $avatarFile = $form->get('avatar')->getData();
            // Si avatarFile existe :
            if ($avatarFile) {
                // Envoie les données au Service FileUploader 
                $avatarFileName = $fileUploader->upload($avatarFile);
                $user->setAvatar($avatarFileName);
                $entityManager->persist($user);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/index.html.twig', [
            'form' => $form
        ]);
    }
}
