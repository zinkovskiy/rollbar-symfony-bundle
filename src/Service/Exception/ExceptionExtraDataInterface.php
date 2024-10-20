<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Service\Exception;

use Throwable;

interface ExceptionExtraDataInterface extends Throwable
{
    /** @return array<string, mixed> */
    public function getExtraData(): array;
}
