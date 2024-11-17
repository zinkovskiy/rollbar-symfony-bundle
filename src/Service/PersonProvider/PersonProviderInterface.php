<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\PersonProvider;

/**
 * Checks whether to ignore exception or not.
 */
interface PersonProviderInterface
{
    public function getUserInfo(): mixed;
}
