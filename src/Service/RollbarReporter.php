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
    public function reportEmergency(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::EMERGENCY, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function reportAlert(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::ALERT, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function reportCritical(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::CRITICAL, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function reportError(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::ERROR, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function reportWarning(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::WARNING, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function reportNotice(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::NOTICE, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function reportInfo(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::INFO, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function reportDebug(Throwable $throwable, array $extraData = []): void
    {
        $this->report($throwable, Level::DEBUG, $extraData);
    }

    /** @param array<string, mixed> $extraData */
    public function report(Throwable $throwable, string $level, array $extraData = []): void
    {
        $customDataMethodContext = [
            'exception' => $throwable, // We need to pass the exception to the context to have the exception trace for occurrence details.
        ];
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
