<?php

namespace App\Controller;

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
        '/privacy-policy', 
        name: 'privacy_policy', 
        options: ['sitemap' => ['priority' => 0.8, 'section' => 'base']]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function privacyPolicy(Request $request): Response {
        return $this->render('static/privacy_policy.html.twig');
    }

    #[Route(
        '/terms-of-service', 
        name: 'terms_of_service', 
        options: ['sitemap' => ['priority' => 0.8, 'section' => 'base']]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function termsOfService(Request $request): Response {
        return $this->render('static/terms_of_service.html.twig');
    }
}