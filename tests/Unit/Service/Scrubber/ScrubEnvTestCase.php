<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use PHPUnit\Framework\TestCase;

class ScrubEnvTestCase extends TestCase
{
    protected const APP_SECRET = '37d2f99b0fcffa4818228715851a68a2';
    protected const DATABASE_DSN = 'postgresql://127.0.0.1:5432/test';
    protected const REDIS_DATABASE_NUMBER = 15;

    protected function setUp(): void
    {
        $_ENV['SYMFONY_DOTENV_VARS'] = 'APP_ENV,APP_SECRET,REDIS_DATABASE_NUMBER';
        $_ENV['APP_ENV'] = 'prod';
        $_ENV['APP_SECRET'] = self::APP_SECRET;
        $_ENV['DATABASE_DSN'] = self::DATABASE_DSN; // imagine that this env variable set outside, not via symfony secrets
        $_ENV['REDIS_DATABASE_NUMBER'] = self::REDIS_DATABASE_NUMBER;
    }

    protected function tearDown(): void
    {
        unset($_ENV['SYMFONY_DOTENV_VARS']);
        unset($_ENV['APP_ENV']);
        unset($_ENV['APP_SECRET']);
        unset($_ENV['DATABASE_DSN']);
        unset($_ENV['REDIS_DATABASE_NUMBER']);
    }
}
