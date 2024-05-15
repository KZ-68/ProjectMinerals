<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\SelectLanguageType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route(
        '/{_locale}/register', 
        name: 'app_register',
        requirements: [
            '_locale' => 'en|fr',
        ],
    )]
    public function register(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/"; 
            $plainPassword = $form->get('plainPassword')->getData();
            if(preg_match($password_regex, $plainPassword)) {
                $agreeTerms = $form->get('agreeTerms')->getData();
                if($agreeTerms === true) {
                    // encode the plain password
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $plainPassword
                        )
                    );

                    $entityManager->persist($user);
                    $entityManager->flush();

                    // generate a signed url and email it to the user
                    $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                        (new TemplatedEmail())
                            ->from(new Address('kz-minerals-mailer@exemple.com', 'KZ Minerals Mailer'))
                            ->to($user->getEmail())
                            ->subject('Please Confirm your Email')
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                    );

                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
                } else {
                    $this->addFlash('error', 'We need your consent for the registration');
                    return $this->redirectToRoute('app_register');
                }
            } else {
                $this->addFlash('error', 'Password strength minimal requirement needed');
                return $this->redirectToRoute('app_register');
            }
            
        }

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/register');
            } else {
                return $this->redirect('/en/register');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'langForm' => $langForm
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
