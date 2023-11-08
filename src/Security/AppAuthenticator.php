<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        if(isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:8000' || $_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:8001') {
            if($request->isMethod('POST')) {
                if ($request->request->get('raison', '') !== null && empty($request->request->get('raison', ''))) {
                    if 
                    (
                        $request->request->get('email', '') !== null && !empty($request->request->get('email', '')) &&
                        $request->request->get('password', '') !== null && !empty($request->request->get('password', ''))
                    ) 
                    {
                        $email = $request->request->get('email', '');

                        $request->getSession()->set(Security::LAST_USERNAME, $email);

                        return new Passport(
                            new UserBadge($email),
                            new PasswordCredentials($request->request->get('password', '')),
                            [
                                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                                new RememberMeBadge(),
                            ]
                        );
                    } else {
                        throw new AccessDeniedHttpException();
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

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Si le chemin de l'URL force l'utilisateur à se connecter.
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {

            $user = $token->getUser();
            // Si le chemin contient une route vers la page admin mais que l'utilisateur n'a pas le rôle ADMIN :
            if(str_contains($targetPath, '/admin') && !in_array('ROLE_ADMIN', $user->getRoles())) {
                // Redirection vers la homepage
                return new RedirectResponse($this->urlGenerator->generate('home_index'));
            // Si le chemin contient une route vers la page admin et que l'utilisateur est administrateur :
            } else if (str_contains($targetPath, '/admin') && in_array('ROLE_ADMIN', $user->getRoles())) {
                // Redirection vers la page cible
                return new RedirectResponse($targetPath);
            } else {
                // Redirection par défaut
                return new RedirectResponse($targetPath);
            }
           
        } 

        return new RedirectResponse($this->urlGenerator->generate('home_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
