<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEmailType;
use App\Entity\Notification;
use App\Service\FileUploader;
use App\Form\UserPasswordType;
use App\Form\UserUsernameType;
use App\Form\SelectLanguageType;
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
    #[Route(
        '/{_locale}/profile', 
        name: 'app_profile',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    public function index
    (
    Request $request, FileUploader $fileUploader, 
    EntityManagerInterface $entityManager, FavoriteRepository $favoriteRepository
    ): Response
    {

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

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

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/profile');
            } else {
                return $this->redirect('/en/profile');
            }
        }

        return $this->render('user/index.html.twig', [
            'form' => $form,
            'favorites' => $favorites,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/profile/settings/', 
        name: 'settings_profile', 
        requirements: [
            '_locale' => 'en|fr'
        ],
        methods:['GET', 'POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function editSettings(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {

        $user = $this->getUser();

        // Fait une redirection si aucun utilisateur en session est trouvé
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Création des deux formulaires séparés
        $form1name = $this->createForm(UserEmailType::class, $user);
        $form2name = $this->createForm(UserPasswordType::class, $user);
        $form3name = $this->createForm(UserUsernameType::class, $user);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/profile/settings/');
            } else {
                return $this->redirect('/en/profile/settings/');
            }
        }

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
                        
                        return $this->redirectToRoute('settings_profile');
                        
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
            'form3name' => $form3name,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/profile/settings/popup', 
        name: 'settings_delete_popup'
    )]
    public function popupDeleteAccount(Request $request): Response {
        
        $user = $this->getUser();

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('_partials/_delete_account.html.twig');
    }

    #[Route(
        '/profile/settings/popup/delete', 
        name: 'settings_delete_profile', 
        methods:['POST']
    )]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager, Session $session, TokenStorageInterface $tokenStorage): Response {

        $user = $this->getUser();

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            if($user){
                $entityManager->remove($user);
                $entityManager->flush();
                $tokenStorage->setToken(null);
                $session->invalidate();
                return $this->redirectToRoute('home_index');
            }
        }
    }

    #[Route(
        '/profile/notifications', 
        name: 'notifications_center', 
        methods:['GET']
    )]
    public function notifications(Request $request, NotificationRepository $notificationRepository) : Response {

        $user = $this->getUser();

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        if ($request->isMethod('GET')) {
                $notifications = $notificationRepository->findNotificationsByUser($user);
        }
        return $this->render('user/_notifications_modal.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route(
        '/profile/notifications/{id}/read', 
        name: 'read_notification'
    )]
    public function readNotifications(Request $request, EntityManagerInterface $entityManager, Notification $notification) : Response {

        $user = $this->getUser();

        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        if ($request->isMethod('GET')) {
                
            if (!$notification->isIsRead(true)) {

                $notification->setIsRead(true);
            
                $entityManager->persist($notification);
                $entityManager->flush();
            } 
            
            return $this->redirectToRoute('discussion_mineral', ['slug' => $notification->getComment()->getDiscussion()->getMineral()->getSlug(), 'discussionSlug' => $notification->getComment()->getDiscussion()->getSlug()]);

        } else {
            return $this->redirectToRoute('app_profile');
        }
    }
}
