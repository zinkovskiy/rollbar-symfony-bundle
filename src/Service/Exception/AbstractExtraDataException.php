<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Exception;

use Exception;

abstract class AbstractExtraDataException extends Exception implements ExceptionExtraDataInterface
{
    /** @var array<string, mixed> */
    protected array $extraData = [];


    /** @return array<string, mixed> */
    abstract protected function getExceptionExtraData(): array;

    /** @return array<string, mixed> */
    public function getExtraData(): array
    {
        return array_merge($this->extraData, $this->getExceptionExtraData());
    }

    public function addExtraDataItem(string $key, mixed $extraData): self
    {
        $this->extraData[$key] = $extraData;

        return $this;
    }
}
