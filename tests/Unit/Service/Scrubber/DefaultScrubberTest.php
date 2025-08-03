<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use PHPUnit\Framework\TestCase;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\DefaultScrubber;

class DefaultScrubberTest extends TestCase
{
    use SymfonyDotenvTrait;

    /** @test */
    public function scrubSpecificVariable(): void
    {
        $this->setSymfonyEnvVars();

        $data = json_decode(
            file_get_contents(__DIR__.'/../../data/scrubber/DefaultScrubber/http-request-with-env-vars.json'),
            true
        );

        $expectedData = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/DefaultScrubber/http-request-scrubbed-specific-env-var.json'
            ),
            true
        );

        $service = new DefaultScrubber(['APP_SECRET'], [], false);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($expectedData, $actualData);

        $this->unsetSymfonyEnvVars();
    }

    /** @test */
    public function scrubAllEnvVariables(): void
    {
        $this->setSymfonyEnvVars();

        $data = json_decode(
            file_get_contents(__DIR__.'/../../data/scrubber/DefaultScrubber/http-request-with-env-vars.json'),
            true
        );

        $expectedData = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/DefaultScrubber/http-request-scrubbed-all-env-vars.json'
            ),
            true
        );

        $service = new DefaultScrubber([], [], true);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($expectedData, $actualData);

        $this->unsetSymfonyEnvVars();
    }

    /** @test */
    public function scrubAllEnvVariablesExceptWhitelisted(): void
    {
        $this->setSymfonyEnvVars();

        $data = json_decode(
            file_get_contents(__DIR__.'/../../data/scrubber/DefaultScrubber/http-request-with-env-vars.json'),
            true
        );

        $expectedData = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/DefaultScrubber/http-request-scrubbed-all-except-whitelisted.json'
            ),
            true
        );

        $service = new DefaultScrubber([], ['APP_SECRET'], true);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($expectedData, $actualData);

        $this->unsetSymfonyEnvVars();
    }
}
