<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Color;
use App\Entity\Lustre;
use App\Entity\Mineral;
use App\Entity\Category;
use App\Form\AddColorType;
use App\Form\CategoryType;
use App\Form\AddLustreType;
use App\Repository\UserRepository;
use App\Repository\ColorRepository;
use App\Repository\LustreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

            return $this->redirectToRoute('app_admin_color');
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

            return $this->redirectToRoute('app_admin_color');
        }

        return $this->render('admin/color/edit_color.html.twig', [
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

            return $this->redirectToRoute('app_admin_lustre');
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

            return $this->redirectToRoute('app_admin_lustre');
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

        return $this->redirectToRoute('app_admin_lustre');
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

        return $this->render('admin/category/new_category.html.twig', [
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

        return $this->render('admin/category/edit_category.html.twig', [
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
    public function usersList(UserRepository $userRepository, Request $request): Response
    {
        return $this->render('admin/user/users_list.html.twig', [
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

}
