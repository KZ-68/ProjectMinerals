<?php

namespace App\Controller;

use App\Form\SelectLanguageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StaticController extends AbstractController
{
    /**
     * Displays robots.txt.
     */
    #[Route(
        "/robots.txt", 
        name: "app_robots"
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function robotsAction($template = null)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');

        return $this->render("robots.txt.twig");
    }

    #[Route(
        '/{_locale}/privacy-policy', 
        name: 'privacy_policy', 
        options: ['sitemap' => ['priority' => 0.8, 'section' => 'base']],
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function privacyPolicy(Request $request): Response {
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/privacy-policy');
            } else {
                return $this->redirect('/en/privacy-policy');
            }
        }
        return $this->render('static/privacy_policy.html.twig', [
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/terms-of-service', 
        name: 'terms_of_service', 
        options: ['sitemap' => ['priority' => 0.8, 'section' => 'base']],
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function termsOfService(Request $request): Response {
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/terms-of-service');
            } else {
                return $this->redirect('/en/terms-of-service');
            }
        }
        return $this->render('static/terms_of_service.html.twig', [
            'langForm' => $langForm
        ]);
    }
}