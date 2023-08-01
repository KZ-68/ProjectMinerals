<?php

namespace App\Controller;

use App\Entity\Mineral;
use App\Entity\Category;
use App\Repository\MineralRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function showMineral(Mineral $mineral): Response
    {
        return $this->render('mineral/showMineral.html.twig', [
            'mineral' => $mineral
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
