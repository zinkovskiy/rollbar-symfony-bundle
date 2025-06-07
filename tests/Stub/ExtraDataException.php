<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Stub;

use Exception;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataInterface;

class ExtraDataException extends Exception implements ExceptionExtraDataInterface
{
    public function getExtraData(): array
    {
        return [
            'key' => 'value',
        ];
    }
}
