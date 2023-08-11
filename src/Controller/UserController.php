<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEmailType;
use App\Service\FileUploader;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    #[Route('/profile/settings/{id}', name: 'settings_profile', methods:['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editSettings(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_session');
        }

        $form1name = $this->createForm(UserEmailType::class, $user);

        $form2name = $this->createForm(UserPasswordType::class, $user);

        if ($request->isMethod('POST')) {
        
            $form1name->handleRequest($request);
        
            if ($form1name->isSubmitted() && $form1name->isValid()) {
                $email = $form1name->get('email')->getData();
                    $confirmEmail = $form1name->get('confirmEmail')->getData();
                    if($confirmEmail === $email) {
                        $user = $form1name->getData();
                        $entityManager->persist($user);
                        $entityManager->flush();
                        
                        $this->addFlash(
                            'success', 
                            'Email adress has changed with success !'
                        );
            
                        return $this->redirectToRoute('settings_profile', ['id' => $user->getId()]);

                    } else {
                        $this->addFlash(
                            'warning', 
                            'The informations submited are incorrects.'
                        );
                    }
            } 
            
            $form2name->handleRequest($request);

            if ($form2name->isSubmitted() && $form2name->isValid()) {
                $oldPassword = $form2name->get('plainPassword')->getData();
                $newPassword = $form2name->get('newPassword')->getData();
    
                if ($hasher->isPasswordValid($user, $oldPassword)) {
                    $encodedPassword = $hasher->hashPassword(
                        $user,
                        $newPassword
                    );
                    $user->setPassword($encodedPassword);
                    $entityManager->flush();
                    
                    $this->addFlash(
                        'success', 
                        'The password has been modified with success !'
                    );
                    
                    return $this->redirectToRoute('settings_profile', ['id' => $user->getId()]);

                } else {
                    die(dump($oldPassword));
                    $this->addFlash(
                        'warning', 
                        'The informations submited are incorrects.'
                    );
                }
                
            }
            
        }
        return $this->render('user/settingsProfile.html.twig', [
            'form1name' => $form1name,
            'form2name' => $form2name
        ]);
    }
}
