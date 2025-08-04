<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\DefaultScrubber;

class DefaultScrubberTest extends ScrubEnvTestCase
{
    /** @test */
    public function scrubSpecificVariable(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__.'/../../data/scrubber/DefaultScrubber/http-request-with-env-vars.json'),
            true
        );

        $service = new DefaultScrubber(['APP_SECRET'], [], false);

        $actualData = $service->scrub($data, '***');

        $actualDataJson = json_encode($actualData, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsStringIgnoringCase(self::APP_SECRET, $actualDataJson);
        $this->assertStringContainsStringIgnoringCase(str_replace('/', '\\/', self::DATABASE_DSN), $actualDataJson);
    }

    /** @test */
    public function scrubAllEnvVariables(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__.'/../../data/scrubber/DefaultScrubber/http-request-with-env-vars.json'),
            true
        );

        $service = new DefaultScrubber([], [], true);

        $actualData = $service->scrub($data, '***');

        $actualDataJson = json_encode($actualData, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsStringIgnoringCase(self::APP_SECRET, $actualDataJson);
        $this->assertStringNotContainsStringIgnoringCase(str_replace('/', '\\/', self::DATABASE_DSN), $actualDataJson);
    }

    /** @test */
    public function scrubAllEnvVariablesExceptWhitelisted(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__.'/../../data/scrubber/DefaultScrubber/http-request-with-env-vars.json'),
            true
        );

        $service = new DefaultScrubber([], ['APP_SECRET'], true);

        $actualData = $service->scrub($data, '***');

        $actualDataJson = json_encode($actualData, JSON_THROW_ON_ERROR);

        $this->assertStringContainsStringIgnoringCase(self::APP_SECRET, $actualDataJson);
        $this->assertStringNotContainsStringIgnoringCase(str_replace('/', '\\/', self::DATABASE_DSN), $actualDataJson);
    }
}
