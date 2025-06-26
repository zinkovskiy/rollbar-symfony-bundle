<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Integration;

use SFErTrack\RollbarSymfonyBundle\Tests\App\Service\Rollbar\ExtraDataExceptionProviderWrapper;
use SFErTrack\RollbarSymfonyBundle\Tests\App\Service\Rollbar\Scrubber;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CatchExceptionTest extends WebTestCase
{
    public function catchException(): void
    {
        $client = static::createClient();

        $client->request('GET', '/throw-exception');

        /** @var Scrubber $scrubber */
        $scrubber = $client->getContainer()
            ->get(Scrubber::class);

        $countScrubberCalls = $scrubber->getCountCalls();

        $this->assertResponseStatusCodeSame(500);
        $this->assertEquals(1, $countScrubberCalls);
    }

    /** @test */
    public function catchExceptionWithExtendedExtraData(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pay-order/0197ac12-8464-7dca-8a6d-646a9a43a4e8');

        /** @var ExtraDataExceptionProviderWrapper $wrapper */
        $wrapper = $client->getContainer()
            ->get(ExtraDataExceptionProviderWrapper::class);

        $extraData = $wrapper->getLastExceptionExtraData();

        $this->assertResponseStatusCodeSame(500);
        $this->assertArrayHasKey('customer_id', $extraData);
        $this->assertArrayHasKey('request_payload', $extraData);
    }
}
