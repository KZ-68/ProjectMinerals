<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[IsGranted('PUBLIC_ACCESS')]
class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if(isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:8000' || $_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:8001') {
                if($request->isMethod('POST')) {
                    if ($form->get('raison')->getData() == null && empty($form->get('raison')->getData())) {
                        if 
                        (
                            $form->get('lastName')->getData() !== null && !empty($form->get('lastName')->getData()) &&
                            $form->get('firstName')->getData() !== null && !empty($form->get('firstName')->getData()) &&
                            $form->get('email')->getData() !== null && !empty($form->get('email')->getData()) &&
                            $form->get('subject')->getData() !== null && !empty($form->get('subject')->getData()) &&
                            $form->get('message')->getData() !== null && !empty($form->get('message')->getData())
                        ) 
                        {
                            $contact = $form->getData();
                            $entityManager->persist($contact);
                            $entityManager->flush();

                            // Envoi d'un email à l'admin du site
                            $email = (new Email())
                            ->from($contact->getEmail())
                            ->to(new Address('kz-minerals-admin@exemple.com', 'Admin Site'))
                            ->subject($contact->getSubject())
                            ->html($contact->getMessage());

                            $mailer->send($email);

                            $this->addFlash('success', 'Your contact message has been sent with success !');

                            return $this->redirectToRoute('app_contact');
                        }
                    } else {
                        throw new AccessDeniedHttpException();
                    }
                    
                } else {
                    throw new MethodNotAllowedException(['method' => 'POST']);
                }
            } else {
                throw new AccessDeniedHttpException();
            }
        
        }
        
        return $this->render('user/contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
