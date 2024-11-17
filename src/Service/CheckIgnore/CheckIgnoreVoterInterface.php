<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\CheckIgnore;

use Rollbar\Payload\Payload;

/**
 * Checks whether to ignore the exception or not.
 */
interface CheckIgnoreVoterInterface
{
    /**
     * Method implementation should return true if exception should be ignored, false - otherwise
     */
    public function shouldIgnore(bool $isUncaught, mixed $toLog, Payload $payload): bool;
}
