<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Exception;

use Exception;
use SFErTrack\RollbarSymfonyBundle\Service\UserFriendlyExceptionInterface;

final class TestException extends Exception implements UserFriendlyExceptionInterface
{
    public function __construct()
    {
        parent::__construct('Test user friendly exception');
    }
}
