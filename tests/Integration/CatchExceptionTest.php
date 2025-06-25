<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Integration;

use SFErTrack\RollbarSymfonyBundle\Tests\App\Service\Scrubber;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CatchExceptionTest extends WebTestCase
{
    /** @test */
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
    public function catchUserFriendlyException(): void
    {
        $client = static::createClient();

        $client->request('GET', '/throw-user-friendly-exception');

        /** @var Scrubber $scrubber */
        $scrubber = $client->getContainer()
            ->get(Scrubber::class);

        $countScrubberCalls = $scrubber->getCountCalls();

        $this->assertResponseStatusCodeSame(500);
        $this->assertEquals(0, $countScrubberCalls);
    }
}
