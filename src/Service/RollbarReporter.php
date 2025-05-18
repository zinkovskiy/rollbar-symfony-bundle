<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service;

use Rollbar\Payload\Level;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataInterface;
use Throwable;

final class RollbarReporter
{
    public function __construct(private readonly RollbarWrapper $rollbarWrapper) {}

    public function reportError(Throwable $throwable, string $level = Level::ERROR): void
    {
        $customDataMethodContext = [];
        if ($throwable instanceof ExceptionExtraDataInterface) {
            $customDataMethodContext['custom_data_method_context'] = $throwable->getExtraData();
        }

        $this->rollbarWrapper->log(
            $level,
            $throwable->getMessage(),
            $customDataMethodContext
        );
    }
}
