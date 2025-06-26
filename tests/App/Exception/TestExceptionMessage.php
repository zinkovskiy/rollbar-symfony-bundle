<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Exception;

use Exception;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\UserFriendlyExceptionMessageInterface;

final class TestExceptionMessage extends Exception implements UserFriendlyExceptionMessageInterface
{
    public function __construct()
    {
        parent::__construct('Test user friendly exception');
    }
}
