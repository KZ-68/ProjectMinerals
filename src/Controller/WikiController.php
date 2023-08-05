<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Image;
use App\Entity\Mineral;
use App\Entity\Variety;
use App\Entity\Category;
use App\Form\MineralType;
use App\Form\VarietyType;
use App\Service\FileUploader;
use App\Form\MineralColorType;
use App\Repository\ColorRepository;
use App\Repository\ImageRepository;
use App\Repository\MineralRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WikiController extends AbstractController
{
    #[Route('/wiki/mineral', name: 'app_mineral')]
    public function index(MineralRepository $mineralRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $pagination = $paginator->paginate(
            $mineralRepository->getPaginationQuery(),
            $request->query->get('page', 1),
            10
        );

        return $this->render('wiki/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/wiki/mineral/{id}/show', name: 'show_mineral')]
    public function showMineral(Mineral $mineral, ImageRepository $imageRepository ): Response
    {
        $image = $imageRepository->findImagesById($mineral->getId());
        $images = $imageRepository->findImagesAndNameInMineral($mineral->getId());
        return $this->render('wiki/show_mineral.html.twig', [
            'image' => $image,
            'images' => $images,
            'mineral' => $mineral
        ]);
    }

    #[Route('/wiki/mineral/new', name: 'new_mineral')]
    // #[IsGranted('ROLE_')]
    public function new_mineral(FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        $mineral = new Mineral();

        $form = $this->createForm(MineralType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $newFileName = $fileUploader->upload($image);
                $img = new Image;
                $img->setFileName($newFileName);
                $mineral->addImage($img);
            }

            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('app_mineral');
        }

        return $this->render('wiki/new_mineral.html.twig', [
            'form' => $form,
            'mineralId' => $mineral->getId()
        ]);
    }

    #[Route('/wiki/mineral/{id}/edit', name: 'edit_mineral')]
    public function edit(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {
        $originalColors = new ArrayCollection();

        foreach ($mineral->getColors() as $color) {
            $originalColors->add($color);
        }

        $editForm = $this->createForm(MineralType::class, $mineral);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // remove the relationship between the tag and the mineral
            foreach ($originalColors as $color) {
                if (false === $mineral->getColors()->contains($color)) {
                    // remove the mineral from the color
                    $color->getMinerals()->removeElement($mineral);

                    // if it was a many-to-one relationship, remove the relationship like this
                    // $color->setMineral(null);

                    $entityManager->persist($color);
                }
            }

            $entityManager->persist($mineral);
            $entityManager->flush();

            // redirect back to some edit page
            return $this->redirectToRoute('edit_mineral', ['id' => $mineral->getId()]);

        }

        return $this->render('wiki/new_mineral.html.twig', [
            'form' => $editForm,
            'mineralId' => $mineral->getId()
        ]);
    } 

    #[Route('/wiki/mineral/{id}/variety/new', name: 'new_variety')]
    // #[IsGranted('ROLE_')]
    public function new_variety(Variety $variety = null, Mineral $mineral, FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        $variety = new Variety();
        
        $form = $this->createForm(VarietyType::class, $variety);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $variety = $form->getData();
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $newFileName = $fileUploader->upload($image);
                $img = new Image;
                $img->setFileName($newFileName);
                $variety->addImage($img);
                $mineral->addImage($img);
            }

            $variety->setMineral($mineral);
            $entityManager->persist($variety);
            $entityManager->flush();

            return $this->redirectToRoute('app_variety');
        }

        return $this->render('wiki/new_variety.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{id}/show/editColors', name: 'edit_mineral_colors')]
    public function edit_mineral_colors(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(MineralColorType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['id' => $mineral->getId()]);
        }

        return $this->render('wiki/edit_mineral_colors.html.twig', [
            'form' => $form,
            'edit' => $mineral->getId()
        ]);
    }

    #[Route('/wiki/mineral/{id}/show/editLustres', name: 'edit_mineral_lustres')]
    public function edit_mineral_lustres(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(LustreType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['id' => $mineral->getId()]);
        }

        return $this->render('wiki/edit_mineral_lustres.html.twig', [
            'form' => $form,
            'edit' => $mineral->getId()
        ]);
    }
    
    #[Route('/wiki/category', name: 'app_category')]
    public function categorieslist(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ["name" => "ASC"]);
        return $this->render('wiki/categories_list.html.twig', [
            'categories' => $categories
        ]);
    }

    

    #[Route('/wiki/category/{id}/show', name: 'show_category')]
    public function showCategory(Category $category): Response
    {
        return $this->render('category/show_category.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/wiki/color', name: 'list_colors')]
    public function colorsList(ColorRepository $colorRepository): Response
    {
        $colors = $colorRepository->findBy([], ["name" => "ASC"]);
        
        return $this->render('wiki/colors_list.html.twig', [
            'colors' => $colors
        ]);
    }

    #[Route('/wiki/color/{id}/show', name: 'show_color')]
    public function showColor(Color $color): Response
    {
        return $this->render('wiki/show_color.html.twig', [
            'color' => $color
        ]);
    }

}
