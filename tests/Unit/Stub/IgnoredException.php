<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Stub;

use Exception;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\IgnoreExceptionInterface;

class IgnoredException extends Exception implements IgnoreExceptionInterface {}
