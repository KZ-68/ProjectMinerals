<?php

namespace App\Controller;

use App\Form\SelectLanguageType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(
        path: '/{_locale}/login', 
        name: 'app_login',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/login');
            } else {
                return $this->redirect('/en/login');
            }
        }
        
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        path: '/{_locale}/logout', 
        name: 'app_logout',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
