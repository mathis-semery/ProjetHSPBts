<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Redirige les utilisateurs en attente de vérification vers les pages appropriées
 * - ROLE_ATTENTE_VERIFICATION -> /verification
 * - ROLE_ATTENTE -> /attente
 */
class AttenteRedirectSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser()) {
            return;
        }

        $user = $token->getUser();
        $roles = $user->getRoles();
        $currentRoute = $event->getRequest()->attributes->get('_route');

        // Routes autorisées pour les utilisateurs en attente
        $allowedRoutes = [
            'app_verification_request',
            'app_attente_verification',
            'app_attente',
            'app_logout',
            '_profiler',
            '_wdt',
        ];

        // Ne pas rediriger si on est déjà sur une route autorisée
        foreach ($allowedRoutes as $route) {
            if (str_starts_with($currentRoute ?? '', $route)) {
                return;
            }
        }

        // Utilisateurs en attente de vérification -> redirection vers /verification
        if (in_array('ROLE_ATTENTE_VERIFICATION', $roles, true)) {
            $event->setResponse(
                new RedirectResponse($this->urlGenerator->generate('app_verification_request'))
            );
            return;
        }

        // Utilisateurs en quarantaine -> redirection vers /attente
        if (in_array('ROLE_ATTENTE', $roles, true)) {
            $event->setResponse(
                new RedirectResponse($this->urlGenerator->generate('app_attente'))
            );
        }
    }
}
