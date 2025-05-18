<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Service\Scrubber;

use PHPUnit\Framework\TestCase;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\CookieScrubber;

class CookieScrubberTest extends TestCase
{
    /** @test */
    public function scrubConsoleExceptionData(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__ . '/../../data/scrubber/console-exception-data.json'),
            true
        );

        $service = new CookieScrubber(['key']);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($data, $actualData);
    }

    /** @test */
    public function scrubFieldsNotConfigured(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__ . '/../../data/scrubber/http-request-with-cookies-exception-data.json'),
            true
        );

        $service = new CookieScrubber([]);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($data, $actualData);
    }

    /** @test */
    public function scrubValue(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__ . '/../../data/scrubber/http-request-with-cookies-exception-data.json'),
            true
        );

        $service = new CookieScrubber(['key']);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals('test=test;key=***;_ga=njdaqwl14', $actualData['request']['headers']['Cookie']);
    }

    /** @test */
    public function scrubEmptyCookies(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__ . '/../../data/scrubber/http-request-without-cookies-exception-data.json'),
            true
        );

        $service = new CookieScrubber(['key']);

        $actualData = $service->scrub($data, '***');
        $this->assertEquals($data, $actualData);
    }
}
