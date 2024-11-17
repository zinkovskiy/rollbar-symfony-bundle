<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Exception;

use Throwable;

interface ExceptionExtraDataProviderInterface
{
    /**
     * @param array<string, mixed>|null $context
     */
    public function supports(Throwable|string $toLog, ?array $context): bool;

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    public function getExtraData(Throwable|string $toLog, ?array $context): array;
}
