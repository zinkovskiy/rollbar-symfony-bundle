<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Exception;

/**
 * Used to distinguish exceptions where the error message
 * can be safely shown to users from all other exceptions.
 */
interface UserFriendlyExceptionMessageInterface
{
    public function getUserFriendlyMessage(): string;
}
