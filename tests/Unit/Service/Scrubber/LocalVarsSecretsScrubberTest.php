<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use PHPUnit\Framework\TestCase;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\LocalVarsSecretsScrubber;

class LocalVarsSecretsScrubberTest extends TestCase
{
    use SymfonyDotenvTrait;

    /** @test */
    public function scrubSecretEnvVariableValue(): void
    {
        $this->setSymfonyEnvVars();

        $data = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/LocalVarsSecretsScrubber/console-exception-with-secret-value-in-local-var.json'
            ),
            true
        );

        $expectedData = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/LocalVarsSecretsScrubber/console-exception-scrubbed-secret-value-in-local-var.json'
            ),
            true
        );

        $service = new LocalVarsSecretsScrubber([], true);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($expectedData, $actualData);

        $this->unsetSymfonyEnvVars();
    }

    /** @test */
    public function scrubSecretEnvVariableValueFallback(): void
    {
        $this->setSymfonyEnvVars();
        unset($_ENV['SYMFONY_DOTENV_VARS']);

        $data = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/LocalVarsSecretsScrubber/console-exception-with-secret-value-in-local-var-fallback.json'
            ),
            true
        );

        $expectedData = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/LocalVarsSecretsScrubber/console-exception-scrubbed-secret-value-in-local-var-fallback.json'
            ),
            true
        );

        $service = new LocalVarsSecretsScrubber([], true);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($expectedData, $actualData);

        $this->unsetSymfonyEnvVars();
    }
}
