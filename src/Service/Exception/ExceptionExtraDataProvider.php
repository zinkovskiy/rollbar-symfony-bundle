<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Exception;

use Throwable;

final class ExceptionExtraDataProvider implements ExceptionExtraDataProviderInterface
{
    /**
     * @param array<string, mixed>|null $context
     */
    public function supports(Throwable|string $toLog, ?array $context): bool
    {
        return $toLog instanceof ExceptionExtraDataInterface;
    }

    /**
     * @param array<string, mixed>|null $context
     * @return array<string, mixed>
     */
    public function getExtraData(Throwable|string $toLog, ?array $context): array
    {
        if (!$toLog instanceof ExceptionExtraDataInterface) {
            return [];
        }

        return $toLog->getExtraData();
    }
}
