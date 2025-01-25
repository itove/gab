<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            // Redirect admin users to admin dashboard
            return new RedirectResponse($this->urlGenerator->generate('admin'));
        } elseif ($this->authorizationChecker->isGranted('ROLE_USER')) {
            // Redirect regular users to homepage
            return new RedirectResponse($this->urlGenerator->generate('app_index'));
        }

        // Fallback redirect
        return new RedirectResponse($this->urlGenerator->generate('app_index'));
    }
} 
