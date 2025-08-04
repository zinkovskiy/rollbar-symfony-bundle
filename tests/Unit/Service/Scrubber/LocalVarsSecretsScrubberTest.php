<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\LocalVarsSecretsScrubber;

class LocalVarsSecretsScrubberTest extends ScrubEnvTestCase
{
    /** @test */
    public function scrubSecretEnvVariableValue(): void
    {
        $data = json_decode(
            file_get_contents(
                __DIR__.'/../../data/scrubber/LocalVarsSecretsScrubber/console-exception-with-secret-value-in-local-var.json'
            ),
            true
        );

        $service = new LocalVarsSecretsScrubber([], true);

        $actualData = $service->scrub($data, '***');

        $actualDataJson = json_encode($actualData, JSON_THROW_ON_ERROR);

        $this->assertEquals(15, $actualData['data']['body']['trace']['frames'][0]['lineno']);
        $this->assertStringNotContainsStringIgnoringCase(self::APP_SECRET, $actualDataJson);
        $this->assertStringNotContainsStringIgnoringCase(str_replace('/', '\\/', self::DATABASE_DSN), $actualDataJson);
        $this->assertStringNotContainsStringIgnoringCase(self::PAYMENT_PROCESSOR_API_KEY, $actualDataJson);
    }
}
