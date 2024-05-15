<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\SelectLanguageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route(
    '/{_locale}/message', 
    name: 'app_message_',
    requirements: [
        '_locale' => 'en|fr',
    ],
)]
class MessageController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/message/');
            } else {
                return $this->redirect('/en/message/');
            }
        }

        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
            'langForm' => $langForm
        ]);
    }

    #[Route('/send', name:'send')]
    #[IsGranted('ROLE_USER')]
    public function send(Message $message = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if (!$message) {
            $message = new Message();
        }

        // On crée le formulaire
        $form = $this->createForm(MessageType::class, $message);

        // On inspecte la requête du formulaire
        $form->handleRequest($request);
        // Si le formulaire est envoyé et valide :
        if ($form->isSubmitted() && $form->isValid()) {
                // On déclare que l'utilisateur courant est l'expéditeur
                $message->setSender($this->getUser());
                // On récupère les données du formulaire
                $message = $form->getData();
                // On sauvegarde en mémoire la requête pour l'envoi en bdd
                $entityManager->persist($message);
                // On envoie les changements vers la base de données
                $entityManager->flush();

                // On affiche un message d'alerte que le message à bien été envoyé
            $this->addFlash('message', 'Message envoyé avec succès !');
            // On revient à la page des messages
            return $this->redirectToRoute('app_message');
        }

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/message/send');
            } else {
                return $this->redirect('/en/message/send');
            }
        }

        return $this->render('message/send.html.twig', [
            'formSend' => $form,
            'messageId' => $message->getId(),
            'langForm' => $langForm
        ]);
    }

    #[Route('/received', name: 'received')]
    #[IsGranted('ROLE_USER')]
    public function received(Request $request): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/message/received');
            } else {
                return $this->redirect('/en/message/received');
            }
        }

        return $this->render('message/received.html.twig', [
            'langForm' => $langForm
        ]);
    }

    #[Route('/sent', name: 'sent')]
    #[IsGranted('ROLE_USER')]
    public function sent(Request $request): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/message/sent');
            } else {
                return $this->redirect('/en/message/sent');
            }
        }

        return $this->render('message/sent.html.twig', [
            'langForm' => $langForm
        ]);
    }

    #[Route('/received/{id}/read', name: 'read')]
    #[IsGranted('ROLE_USER')]
    public function readMessage(Request $request, Message $message, EntityManagerInterface $entityManager): Response 
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        // Si is_read n'est pas set à true :
        if (!$message->isIsRead(true)) {
            // On set en true la colonne is_read, le message est désormais marqué comme lu
            $message->setIsRead(true);
            // On sauvegarde le changement pour l'envoi en bdd
            $entityManager->persist($message);
            // On envoie le changement dans la bdd
            $entityManager->flush();
        } 

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/message/read');
            } else {
                return $this->redirect('/en/message/read');
            }
        }
        
        return $this->render('message/read.html.twig', [
            'message' => $message,
            'langForm' => $langForm
        ]);
    }

    #[Route('/received/{id}/delete', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function deletemessage(Message $message, EntityManagerInterface $entityManager) {
        
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->redirectToRoute('app_message');
    }
}
