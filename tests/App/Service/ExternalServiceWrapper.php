<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Service;

use SFErTrack\RollbarSymfonyBundle\Tests\App\Exception\BadRequestExtraDataException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class ExternalServiceWrapper
{
    public function __construct(private readonly ExternalService $service) {}

    /**
     * @param array<string, mixed> $requestPayload
     * @throws BadRequestExtraDataException
     */
    public function sendRequest(array $requestPayload): void
    {
        try {
            $this->service->sendRequest($requestPayload);
        } catch (BadRequestException $e) {
            throw new BadRequestExtraDataException($e->getMessage(), $e->getCode(), $e, $requestPayload);
        }
    }
}
