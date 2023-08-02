<?php

namespace App\Controller;

use App\Entity\Mineral;
use App\Entity\Category;
use App\Form\MineralType;
use App\Repository\ImageRepository;
use App\Repository\MineralRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WikiController extends AbstractController
{
    #[Route('/wiki/mineral', name: 'app_mineral')]
    public function index(MineralRepository $mineralRepository): Response
    {
        $minerals = $mineralRepository->findBy([], ["name" => "ASC"]);
        return $this->render('wiki/index.html.twig', [
            'minerals' => $minerals
        ]);
    }

    #[Route('/wiki/mineral/{id}/show', name: 'show_mineral')]
    public function showMineral(Mineral $mineral, ImageRepository $imageRepository ): Response
    {
        $image = $imageRepository->findImagesById($mineral->getId());
        $images = $imageRepository->findImagesAndNameInMineral($mineral->getId());
        return $this->render('wiki/showMineral.html.twig', [
            'image' => $image,
            'images' => $images,
            'mineral' => $mineral
        ]);
    }

    #[Route('/mineral/new', name: 'new_mineral')]
    // #[IsGranted('ROLE_')]
    public function new_mineral(Mineral $mineral = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$mineral) {
            $mineral = new Mineral();
        }

        $form = $this->createForm(MineralType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('app_mineral');
        }

        return $this->render('wiki/new_mineral.html.twig', [
            'form' => $form,
            'mineralId' => $mineral->getId()
        ]);
    }

    #[Route('/mineral/{id}/edit', name: 'edit_mineral')]
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
    
    #[Route('/wiki/category', name: 'app_category')]
    public function categorieslist(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ["name" => "ASC"]);
        return $this->render('wiki/categoriesList.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/wiki/category/{id}/show', name: 'show_category')]
    public function showCategory(Category $category): Response
    {
        return $this->render('category/showCategory.html.twig', [
            'category' => $category
        ]);
    }
}
