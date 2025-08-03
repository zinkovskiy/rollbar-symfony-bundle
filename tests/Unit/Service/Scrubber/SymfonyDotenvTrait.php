<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

trait SymfonyDotenvTrait
{
    protected function setSymfonyEnvVars(): void
    {
        $_ENV['SYMFONY_DOTENV_VARS'] = 'APP_ENV,APP_SECRET';
        $_ENV['APP_ENV'] = 'prod';
        $_ENV['APP_SECRET'] = '37d2f99b0fcffa4818228715851a68a2';
    }

    protected function unsetSymfonyEnvVars(): void
    {
        unset($_ENV['SYMFONY_DOTENV_VARS']);
        unset($_ENV['APP_ENV']);
        unset($_ENV['APP_SECRET']);
    }
}
