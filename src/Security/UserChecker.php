<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {

        if ($user->getVerificationToken()) {
            throw new CustomUserMessageAuthenticationException(
                'Vous devez valider votre email avant de vous connecter.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }
}

