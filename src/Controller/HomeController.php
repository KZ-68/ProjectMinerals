<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Form\AdvancedSearchType;
use App\Form\SelectLanguageType;
use App\Model\AdvancedSearchData;
use App\Repository\ContributionRepository;
use App\Repository\MineralRepository;
use App\Repository\VarietyRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

#[IsGranted('PUBLIC_ACCESS')]
#[Route(
    '/{_locale}/', 
    name: 'home_',
    requirements: [
        '_locale' => 'en|fr',
    ],
)]
class HomeController extends AbstractController
{
    #[Route(
        'home', 
        name: 'index', 
        options: ['sitemap' => ['priority' => 1.0, 'section' => 'home']],
    )]
    public function index(
            MineralRepository $mineralRepository, 
            VarietyRepository $varietyRepository, 
            ContributionRepository $contributionRepository,
            Request $request, 
            PaginatorInterface $paginator
        ): Response
    {
        // Crée un nouvel objet SearchData
        $searchData = new SearchData();

        $advancedSearchData = new AdvancedSearchData();

        // On crée un formulaire avec le modèle SearchType
        $form = $this->createForm(SearchType::class, $searchData);
        $form2 = $this->createForm(AdvancedSearchType::class, $advancedSearchData);
        $langForm = $this->createForm(SelectLanguageType::class);

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
                'form' => $form,
                'minerals' => $minerals,
                'langForm' => $langForm
            ]);
        }

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/home');
            } else {
                return $this->redirect('/en/home');
            }
        }

        $minerals = $mineralRepository->findPaginateMinerals($request->query->getInt('page', 1));
        $mineralsCount = $mineralRepository->findMineralsCount();
        $varietiesCount = $varietyRepository->findVarietiesCount();
        $contributionsCount = $contributionRepository->findContributionsCount();

        if ($request->isXmlHttpRequest()) {
            $formData = $request->request->all();
            $minerals = $mineralRepository->findByAdvancedSearch($formData['advanced_search']);

            $jsonData = [];
            if($minerals === []) {
                $jsonData[] = [
                    'name' => 'No Mineral found in the search engine !'
                ];
            } else {
                foreach ($minerals as $mineral) {
                    $jsonData[] = [
                        'slug' => $mineral->getSlug() ?? null,
                        'name' => $mineral->getName() ?? null
                    ];
                }
            }
            
            $response = [
                'data' => $jsonData
            ];

            return $this->json($response);
        } else {
            return $this->render('home/index.html.twig', [
                'form' => $form,
                'form2' => $form2,
                'langForm' => $langForm,
                'minerals' => $minerals,
                'mineralsCount' => $mineralsCount,
                'varietiesCount' => $varietiesCount,
                'contributionsCount' => $contributionsCount
            ]);
        }

    }

}
