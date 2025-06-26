<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Stub;

use SFErTrack\RollbarSymfonyBundle\Service\Exception\AbstractExtraDataException;

class ExpandedExtraDataException extends AbstractExtraDataException
{
    /** @param array<string, mixed> $data */
    public function __construct(private readonly array $data)
    {
        parent::__construct('Awesome exception message');
    }

    protected function getExceptionExtraData(): array
    {
        return $this->data;
    }
}
