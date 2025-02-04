<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private UrlGeneratorInterface $urlGenerator,
        private RequestStack $requestStack
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Check if there's a requested URL in the session
        // if ($targetPath = $this->requestStack->getSession()->get('_security.main.target_path')) {
        //     $this->requestStack->getSession()->remove('_security.main.target_path');
        //     return new RedirectResponse($targetPath);
        // }

        // Check which firewall was used (admin or main)
        $firewall = $this->getFirewallName($request);

        if ($firewall === 'admin') {
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                return new RedirectResponse($this->urlGenerator->generate('admin'));
            }
            // If not admin, redirect to main login
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }

        // Main firewall logic
        if ($this->authorizationChecker->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->urlGenerator->generate('app_index'));
        }

        // Default fallback
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    private function getFirewallName(Request $request): string
    {
        if (str_starts_with($request->getPathInfo(), '/admin')) {
            return 'admin';
        }
        return 'main';
    }
} 
