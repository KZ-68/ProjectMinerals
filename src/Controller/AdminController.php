<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Color;
use App\Entity\Lustre;
use App\Entity\Mineral;
use App\Entity\Category;
use App\Entity\Discussion;
use App\Form\AddColorType;
use App\Form\CategoryType;
use App\Form\AddLustreType;
use Doctrine\ORM\EntityManager;
use App\Form\ChangeUserRoleType;
use App\Form\SelectLanguageType;
use App\Repository\UserRepository;
use App\Repository\ColorRepository;
use App\Repository\LustreRepository;
use App\Repository\MineralRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[IsGranted('ROLE_ADMIN')]
#[Route(
    '/{_locale}/admin', 
    name: 'app_admin_',
    requirements: [
        '_locale' => 'en|fr',
    ],
)]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        SelectLanguageType $langForm, 
        Request $request
        ): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);
        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/');
            } else {
                return $this->redirect('/en/admin/');
            }
        }
        return $this->render('admin/index.html.twig', [
            'langForm' => $langForm,
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/color', name: 'color')]
    public function colorslist(
        ColorRepository $colorRepository,
        SelectLanguageType $langForm,
        Request $request
        ): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);
        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/color');
            } else {
                return $this->redirect('/en/admin/color');
            }
        }

        return $this->render('admin/color/colors_list.html.twig', [
            'langForm' => $langForm,
            'colors' => $colorRepository->findPaginateColors($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/color/new', name: 'new_color')]
    public function new_color(
        Request $request, 
        EntityManagerInterface $entityManager, 
        SelectLanguageType $langForm
        ): Response
    {
        $color = new Color();

        $form = $this->createForm(AddColorType::class, $color);
        $langForm = $this->createForm(SelectLanguageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $color = $form->getData();

            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_color');
        }
        
        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/color/new');
            } else {
                return $this->redirect('/en/admin/color/new');
            }
        }

        return $this->render('admin/color/new_color.html.twig', [
            'langForm' => $langForm,
            'form' => $form
        ]);
    }

    #[Route('/color/{slug}/edit', name: 'edit_color')]
    public function edit_color(
        Color $color, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        SelectLanguageType $langForm): Response
    {
        $form = $this->createForm(AddColorType::class, $color);
        $langForm = $this->createForm(SelectLanguageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $color = $form->getData();

            $entityManager->persist($color);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_color');
        }

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/color/{slug}/edit');
            } else {
                return $this->redirect('/en/admin/color/{slug}/edit');
            }
        }

        return $this->render('admin/color/edit_color.html.twig', [
            'langForm' => $langForm,
            'form' => $form
        ]);
    }

    #[Route('/color/{id}/delete', name: 'delete_color', methods: ['POST'])]
    public function deleteColor(Color $color, EntityManagerInterface $entityManager, Request $request): Response {

        if (
            !$this->isCsrfTokenValid(
                'delete' . $color->getId(),
                $request->request->get('_token')
        )) {
            throw new BadRequestHttpException();
        }

        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($color);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_color');
    }

    #[Route('/lustre', name: 'lustre')]
    public function lustresList(
        LustreRepository $lustreRepository,
        SelectLanguageType $langForm,
        Request $request
        ): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);
        $langForm->handleRequest($request);

        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/lustre');
            } else {
                return $this->redirect('/en/admin/lustre');
            }
        }

        return $this->render('admin/lustre/lustres_list.html.twig', [
            'langForm' => $langForm,
            'lustres' => $lustreRepository->findPaginateLustres($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/lustre/new', name: 'new_lustre')]
    public function new_lustre(
        Request $request, 
        EntityManagerInterface $entityManager,
        SelectLanguageType $langForm
        ): Response
    {
        $lustre = new Lustre();

        $form = $this->createForm(AddLustreType::class, $lustre);
        $langForm = $this->createForm(SelectLanguageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lustre = $form->getData();

            $entityManager->persist($lustre);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_lustre');
        }
        
        $langForm->handleRequest($request);

        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/lustre/new');
            } else {
                return $this->redirect('/en/admin/lustre/new');
            }
        }

        return $this->render('admin/lustre/new_lustre.html.twig', [
            'langForm' => $langForm,
            'form' => $form
        ]);
    }

    #[Route('/lustre/{slug}/edit', name: 'edit_lustre')]
    public function edit_lustre(
        Lustre $lustre, 
        Request $request, 
        EntityManagerInterface $entityManager,
        SelectLanguageType $langForm
        ): Response
    {
        $form = $this->createForm(AddLustreType::class, $lustre);
        $langForm = $this->createForm(SelectLanguageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lustre = $form->getData();

            $entityManager->persist($lustre);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_lustre');
        }

        $langForm->handleRequest($request);

        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/lustre/{slug}/edit');
            } else {
                return $this->redirect('/en/admin/lustre/{slug}/edit');
            }
        }

        return $this->render('admin/lustre/edit_lustre.html.twig', [
            'langForm' => $langForm,
            'form' => $form
        ]);
    }

    #[Route('/lustre/{slug}/delete', name: 'delete_lustre')]
    public function deleteLustre(Lustre $lustre, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($lustre);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_lustre');
    }

    #[Route('/category/new', name: 'new_category')]
    public function new_category(
        Request $request, 
        EntityManagerInterface $entityManager,
        SelectLanguageType $langForm
        ): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $langForm = $this->createForm(SelectLanguageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        $langForm->handleRequest($request);

        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/category/new');
            } else {
                return $this->redirect('/en/admin/category/new');
            }
        }

        return $this->render('admin/category/new_category.html.twig', [
            'langForm' => $langForm,
            'form' => $form
        ]);
    }

    #[Route('/category/{slug}/edit-category', name: 'edit_category')]
    public function edit_category(
        Category $category, 
        Request $request, 
        EntityManagerInterface $entityManager,
        SelectLanguageType $langForm
        ): Response
    {

        $form = $this->createForm(CategoryType::class, $category);
        $langForm = $this->createForm(SelectLanguageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        $langForm->handleRequest($request);

        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/category/{slug}/edit-category');
            } else {
                return $this->redirect('/en/admin/category/{slug}/edit-category');
            }
        }

        return $this->render('admin/category/edit_category.html.twig', [
            'langForm' => $langForm,
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

    #[Route('/user', name: 'user')]
    public function usersList(
        UserRepository $userRepository,
        SelectLanguageType $langForm,
        Request $request
        ): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);
        $langForm->handleRequest($request);

        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/user');
            } else {
                return $this->redirect('/en/admin/user');
            }
        }

        return $this->render('admin/user/users_list.html.twig', [
            'langForm' => $langForm,
            'users' => $userRepository->findPaginateUsers($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/user/{id}/anonymize', name: 'anonymize_user')]
    public function anonymizeUser(User $user, UserRepository $userRepository): Response
    {
        // Déclare une variable avec le nom d'utilisateur qui sera mis en bdd
        $username = "Deleted User";
        // Récupère la requête du repository pour anonymiser l'utilisateur
        $userRepository->anonymizeUser($user->getId(), $username);

        return $this->redirectToRoute('app_admin_user');
    }

    #[Route('/user/{id}/delete', name: 'delete_user', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager, Request $request): Response {

        // Vérifie si le jeton CSRF est valide
        if (
            !$this->isCsrfTokenValid(
                'delete' . $user->getId(),
                $request->request->get('_token')
        )) {
            throw new BadRequestHttpException();
        }

        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($user);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user');
    }

    #[Route('/discussions-deleted', name: 'discussions_deleted')]
    public function discussionsDeletedList(DiscussionRepository $discussionRepository): Response
    {
        $discussions = $discussionRepository->findBy([], ['content' => 'ASC']);

        return $this->render('admin/discussion/discussions_deleted_list.html.twig', [
            'discussions' => $discussions
        ]);
    }

    #[Route('/edit-role-user', name:'edit_role_user')]
    public function editRoleUser(
        Request $request, 
        UserRepository $userRepository,
        SelectLanguageType $langForm
        ){

        $form = $this->createForm(ChangeUserRoleType::class);
        $langForm = $this->createForm(SelectLanguageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les infos du formulaire
            $selectedUser = $form->get('user')->getData();

            $selectedRole =  $form->get('roles')->getData();

            // On recherche le bon utilisateur dans la couche modèle
            $editUser = $userRepository->find($selectedUser);

            if ($this->getUser() !== $selectedUser) {
                // On met à jour le rôle par une requête préparée. 
                $userRepository->updateRoles($editUser->getId(), $selectedRole);

                return $this->redirectToRoute('app_admin_edit_role_user');
            } else {
                $this->addFlash('error', 'You can\'t change your own role');

                return $this->redirectToRoute('app_admin_edit_role_user');
            }
            
        }

        $langForm->handleRequest($request);

        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/admin/edit-role-user');
            } else {
                return $this->redirect('/en/admin/edit-role-user');
            }
        }

        return $this->render('admin/user/edit_role_user.html.twig', [
                'langForm' => $langForm,
                'editUserForm' => $form
            ]
        );
    }
}
