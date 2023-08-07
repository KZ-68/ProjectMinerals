<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\MineralRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(MineralRepository $mineralRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $searchData = new SearchData();

        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $minerals = $mineralRepository->findBySearch($searchData);
            

            return $this->render('wiki/index.html.twig', [
                'form' => $form->createView(),
                'minerals' => $minerals,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form
        ]);
    }
}
