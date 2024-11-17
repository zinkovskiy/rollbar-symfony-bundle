<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\PersonProvider;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Core\User\UserInterface;

final class PersonProvider implements PersonProviderInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage) {}

    /** @return array<string, mixed>|null|UserInterface */
    public function getUserInfo(): array|null|UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        if ($token instanceof SwitchUserToken) {
            $user = $token->getUser();
            return [
                'id' => $user->getUserIdentifier(),
                'user' => $token->getUser(),
                'impersonator' => $token->getOriginalToken()->getUser(),
            ];
        }

        return $token->getUser();
    }
}
