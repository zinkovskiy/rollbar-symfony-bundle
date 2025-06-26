<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class ExternalService
{
    /** @param array<string, mixed> $requestPayload */
    public function sendRequest(array $requestPayload): void
    {
        // Imagine that external library sends request to API that return bad request exception

        throw new BadRequestException();
    }
}
