<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Service\Rollbar;

use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataProvider;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataProviderInterface;
use Throwable;

final class ExtraDataExceptionProviderWrapper implements ExceptionExtraDataProviderInterface
{
    /** @var array<string, mixed> */
    private array $lastExceptionExtraData = [];

    public function __construct(private readonly ExceptionExtraDataProvider $exceptionExtraDataProvider) {}

    /**
     * @param array<string, mixed>|null $context
     */
    public function supports(Throwable|string $toLog, ?array $context): bool
    {
        return $this->exceptionExtraDataProvider->supports($toLog, $context);
    }

    /**
     * @param array<string, mixed>|null $context
     * @return array<string, mixed>
     */
    public function getExtraData(Throwable|string $toLog, ?array $context): array
    {
        $this->lastExceptionExtraData = $this->exceptionExtraDataProvider->getExtraData($toLog, $context);

        return $this->lastExceptionExtraData;
    }

    /** @return array<string, mixed> */
    public function getLastExceptionExtraData(): array
    {
        return $this->lastExceptionExtraData;
    }
}
