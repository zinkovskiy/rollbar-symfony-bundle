<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Exception;

use SFErTrack\RollbarSymfonyBundle\Service\Exception\AbstractExtraDataException;
use Throwable;

final class BadRequestExtraDataException extends AbstractExtraDataException
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null,
        private readonly array $requestPayload = [],
    ) {
        parent::__construct($message, $code, $previous);
    }

    /** @return array<string, mixed> */
    protected function getExceptionExtraData(): array
    {
        return [
            'request_payload' => $this->requestPayload,
        ];
    }
}
