<?php

declare(strict_types=1);

// This is the namespace of the class under test.
// We are defining a global function here to trick PHP's namespace resolution.

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

/** @return array<string, string> list of env variables, where key is variable name and value - it's value */
function getenv(?string $name = null, bool $local_only = false): array
{
    return [
        'APP_ENV' => 'prod',
        'APP_SECRET' => '37d2f99b0fcffa4818228715851a68a2',
    ];
}

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use PHPUnit\Framework\TestCase;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\DefaultScrubber;

class DefaultScrubberTest extends TestCase
{
    /** @test */
    public function scrubSpecificVariable(): void
    {
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
    }

    /** @test */
    public function scrubAllEnvVariables(): void
    {
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
    }

    /** @test */
    public function scrubAllEnvVariablesExceptWhitelisted(): void
    {
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
    }
}
