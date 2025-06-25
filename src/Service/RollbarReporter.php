<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service;

use Psr\Log\LoggerInterface;
use Rollbar\Payload\Level;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataInterface;
use Throwable;

final class RollbarReporter
{
    public function __construct(private readonly LoggerInterface $rollbarLogger) {}

    /** @param array<string, mixed> $extraData */
    public function reportError(Throwable $throwable, array $extraData = [], string $level = Level::ERROR): void
    {
        $customDataMethodContext = [];
        if ($throwable instanceof ExceptionExtraDataInterface) {
            $extraData = array_merge($extraData, $throwable->getExtraData());
        }

        if ($extraData) {
            $customDataMethodContext['custom_data_method_context'] = $extraData;
        }

        $this->rollbarLogger->log(
            $level,
            $throwable->getMessage(),
            $customDataMethodContext
        );
    }
}
