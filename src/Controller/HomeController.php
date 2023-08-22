<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Form\AdvancedSearchType;
use App\Model\AdvancedSearchData;
use App\Repository\MineralRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('PUBLIC_ACCESS')]
class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(MineralRepository $mineralRepository, Request $request): Response
    {
        // Crée un nouvel objet SearchData
        $searchData = new SearchData();

        $advancedSearchData = new AdvancedSearchData();

        // On crée un formulaire avec le modèle SearchType
        $form = $this->createForm(SearchType::class, $searchData);
        
        $form2 = $this->createForm(AdvancedSearchType::class, $advancedSearchData);

        // On récupère la requête envoyé par le bouton submit
        $form->handleRequest($request);
        // Si le formulaire est envoyé et est valide :
        if ($form->isSubmitted() && $form->isValid()) {
            // Par chaînage, on affecte la valeur 1 de la requête à la page du modèle
            $searchData->page = $request->query->getInt('page', 1);
            // On récupère la requête qui filtre les noms des minéraux
            $minerals = $mineralRepository->findBySearch($searchData);
            // On redirige vers la liste des minéraux et on affiche un rendu :
            return $this->render('wiki/index.html.twig', [
                // Du résultat de la requête
                'minerals' => $minerals,
            ]);
        }

        $form2->handleRequest($request);
        
        if ($form2->isSubmitted() && $form2->isValid()) {
            // Par chaînage, on affecte la valeur 1 de la requête à la page du modèle 
            $advancedSearchData->page = $request->query->getInt('page', 1);
            // On affecte la variable $minerals avec la requête
            $minerals = $mineralRepository->findByAvancedSearch($advancedSearchData);
            // On redirige vers la liste des minéraux et on affiche un rendu :
            return $this->render('wiki/index.html.twig', [
                // Du résultat de la requête
                'minerals' => $minerals,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'form2' => $form2
        ]);
    }

}
