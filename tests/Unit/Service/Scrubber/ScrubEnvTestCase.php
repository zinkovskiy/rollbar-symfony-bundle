<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

use SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber\ScrubEnvTestCase;

function getenv(): array
{
    return [
        'SYMFONY_DOTENV_VARS' => 'APP_ENV,APP_SECRET,REDIS_DATABASE_NUMBER,PAYMENT_PROCESSOR_API_KEY',
        'APP_ENV' => 'prod',
        'APP_SECRET' => ScrubEnvTestCase::APP_SECRET,
        'DATABASE_DSN' => ScrubEnvTestCase::DATABASE_DSN, // imagine that this env variable set outside, not via symfony secret
        'REDIS_DATABASE_NUMBER' => ScrubEnvTestCase::REDIS_DATABASE_NUMBER,
        'PAYMENT_PROCESSOR_API_KEY' => ScrubEnvTestCase::PAYMENT_PROCESSOR_API_KEY,
    ];
}

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use PHPUnit\Framework\TestCase;

class ScrubEnvTestCase extends TestCase
{
    public const APP_SECRET = '37d2f99b0fcffa4818228715851a68a2';
    public const DATABASE_DSN = 'postgresql://127.0.0.1:5432/test';
    public const REDIS_DATABASE_NUMBER = 15;
    public const PAYMENT_PROCESSOR_API_KEY = 'sk_test_jey6SDASDwe46E52asQ';
}
