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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        return $this->render('user/contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
