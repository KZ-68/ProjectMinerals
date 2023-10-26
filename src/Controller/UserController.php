<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEmailType;
use App\Service\FileUploader;
use App\Form\UserPasswordType;
use App\Form\UserUsernameType;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index
    (
    Request $request, FileUploader $fileUploader, 
    EntityManagerInterface $entityManager, FavoriteRepository $favoriteRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupère l'utilisateur courant
        $user = $this->getUser();
        $favorites = $favoriteRepository->findBy(['user' => $user]);
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
            'form' => $form,
            'favorites' => $favorites
        ]);
    }

    #[Route('/profile/settings/{id}', name: 'settings_profile', methods:['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function editSettings(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {

        // Fait une redirection si aucun utilisateur en session est trouvé
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $user) {
            throw new AccessDeniedException;
        }

        // Création des deux formulaires séparés
        $form1name = $this->createForm(UserEmailType::class, $user);
        $form2name = $this->createForm(UserPasswordType::class, $user);
        $form3name = $this->createForm(UserUsernameType::class, $user);

        // Si la requête est bien une méthode POST: 
        if ($request->isMethod('POST')) {
        // Inspecte la requête et appelle le formulaire n°1 soumis
            $form1name->handleRequest($request);
            // Si le foumulaire n°1 est soumis et valide
            if ($form1name->isSubmitted() && $form1name->isValid()) {
                // On récupère l'email dans les données du formulaire
                $email = $form1name->get('email')->getData();
                // On récupère la confirmation de l'email dans les données du formulaire
                $confirmEmail = $form1name->get('confirmEmail')->getData();
                    // Si la confirmation de l'email est pareille que l'email
                    if($confirmEmail === $email) {
                        // On récupère l'utilisateur
                        $user = $form1name->getData();
                        // On prépare l'envoi des données de l'utilisateur en bdd
                        $entityManager->persist($user);
                        // On en envoie les données en bdd
                        $entityManager->flush();
                        // Affiche un message de succès 
                        $this->addFlash(
                            'success', 
                            'Email adress has changed with success !'
                        );
                        // Redirige vers la page des paramètres avec l'id de l'utilisateur
                        return $this->redirectToRoute('settings_profile', ['id' => $user->getId()]);
                      // Si l'email ou la confirmation de l'email ne sont pas identique :  
                    } else {
                        // Affiche un message d'avertissement que les informations sont incorrects
                        $this->addFlash(
                            'warning', 
                            'The informations submited are incorrects.'
                        );
                    }
            } 
            // Inspecte la requête et appelle le formulaire n°2 soumis
            $form2name->handleRequest($request);
            // Si le formulaire n°2 est soumis et valide
            if ($form2name->isSubmitted() && $form2name->isValid()) {
                // On récupère les données du mot de passe courant et le nouveau que l'on souhaite
                $oldPassword = $form2name->get('plainPassword')->getData();
                $newPassword = $form2name->get('newPassword')->getData();
                // Si l'ancien mot de passe et le nouveau ne sont pas identique :
                if ($oldPassword != $newPassword) {
                    // Si le mot de passe courant correspond au mot de passe hashé en bdd :
                    if ($hasher->isPasswordValid($user, $oldPassword)) {
                        /* On déclare une variable qui prends comme valeur 
                        un nouveau hashage créé avec le nouveau mot de passe soumis */
                        $encodedPassword = $hasher->hashPassword(
                            $user,
                            $newPassword
                        );
                        // On ajoute le mot de passe à l'utilisateur
                        $user->setPassword($encodedPassword);
                        // On prépare l'envoi en bdd
                        $entityManager->persist($user);
                        // On envoie les données en bdd
                        $entityManager->flush();

                        $this->addFlash(
                            'success', 
                            'The password has been modified with success !'
                        );
                        
                        return $this->redirectToRoute('settings_profile', ['id' => $user->getId()]);
                        
                    } else {
                        $this->addFlash(
                            'warning', 
                            'The informations submited are incorrects.'
                        );
                    }
                } else {
                    $this->addFlash(
                        'warning',
                        'Old and new password need to be different'
                    );
                }
                    
                
            }

            $form3name->handleRequest($request);
            if ($form3name->isSubmitted() && $form3name->isValid()) {
                $oldUsername = $user->getUsername();
                $newUsername = $form3name->get('username')->getData();

                if ($oldUsername != $newUsername) {
                    $entityManager->persist($user);
                    $entityManager->flush();
                }
            }

        }
        return $this->render('user/settingsProfile.html.twig', [
            'form1name' => $form1name,
            'form2name' => $form2name,
            'form3name' => $form3name
        ]);
    }

    #[Route('/profile/settings/{id}/popup', name: 'settings_delete_popup')]
    public function popupDeleteAccount(User $user, Request $request): Response {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $user) {
            throw new AccessDeniedException;
        }

        return $this->render('_partials/_delete_account.html.twig');
    }

    #[Route('/profile/settings/{id}/popup/delete', name: 'settings_delete_profile', methods:['POST'])]
    public function deleteAccount(User $user, Request $request, EntityManagerInterface $entityManager, Session $session, TokenStorageInterface $tokenStorage): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $user) {
            throw new AccessDeniedException;
        }

        if ($request->isMethod('POST')) {
            $userAuthentified = $this->getUser();
            if($user == $userAuthentified){
                $entityManager->remove($user);
                $entityManager->flush();
                $tokenStorage->setToken(null);
                $session->invalidate();
                return $this->redirectToRoute('home_index');
            }
        }
    }

    #[Route('/profile/{id}/notifications', name: 'notifications_center', methods:['GET'])]
    public function notifications(User $user, Request $request, NotificationRepository $notificationRepository) : Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $user) {
            throw new AccessDeniedException;
        }
        
        if ($request->isMethod('GET')) {
                $notifications = $notificationRepository->findNotificationsByUser($user->getId());
        }
        return $this->render('user/_notifications_modal.html.twig', [
            'notifications' => $notifications
        ]);
    }
}
