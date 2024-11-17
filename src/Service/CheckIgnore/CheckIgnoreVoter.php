<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\CheckIgnore;

use Rollbar\Payload\Payload;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CheckIgnoreVoter implements CheckIgnoreVoterInterface
{
    private const IGNORE_EXCEPTIONS = [
        NotFoundHttpException::class,
        MethodNotAllowedHttpException::class,
        AccessDeniedHttpException::class,
    ];

    public function shouldIgnore(bool $isUncaught, mixed $toLog, Payload $payload): bool
    {
        foreach (self::IGNORE_EXCEPTIONS as $exceptionFQN) {
            if ($toLog instanceof $exceptionFQN || is_subclass_of($toLog, $exceptionFQN)) {
                return true;
            }
        }

        return false;
    }
}
