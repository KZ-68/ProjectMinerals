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
        // Crée un nouvel objet SearchData
        $searchData = new SearchData();

        // On crée un formulaire avec le modèle SearchType
        $form = $this->createForm(SearchType::class, $searchData);
        // On récupère la requête envoyé par le bouton submit
        $form->handleRequest($request);
        // Si le formulaire est envoyé et est valide :
        if ($form->isSubmitted() && $form->isValid()) {
            // Par chaînage, on affecte la valeur 1 de la requête à la page
            $searchData->page = $request->query->getInt('page', 1);
            // On récupère la requête qui filtre les noms des minéraux
            $minerals = $mineralRepository->findBySearch($searchData);

            // On redirige vers la liste des minéraux et on affiche un rendu
            return $this->render('wiki/index.html.twig', [
                // De la vue du formulaire
                'form' => $form,
                // Et du résultat de la requête
                'minerals' => $minerals,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form
        ]);
    }
}
