<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Service\Scrubber;

use Exception;
use PHPUnit\Framework\TestCase;
use Rollbar\ScrubberInterface;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\ScrubberFacade;
use SFErTrack\RollbarSymfonyBundle\Tests\Stub\ServiceInterface;

final class ScrubberFacadeTest extends TestCase
{
    /** @test */
    public function handleExceptionWithBrokenScrubber(): void
    {
        $scrubberMock = $this->getMockBuilder(ScrubberInterface::class)
            ->onlyMethods(['scrub'])
            ->getMock();

        $scrubberMock->expects($this->any())
            ->method('scrub')
            ->willThrowException(new Exception());

        $data = ['key' => 'value'];

        $service = new ScrubberFacade([$scrubberMock]);
        $actualResult = $service->scrub($data, 'xxx');

        $this->assertSame($data, $actualResult);
    }

    /** @test */
    public function scrubberWithoutInterface(): void
    {
        $scrubber = $this->createMock(ServiceInterface::class);

        $scrubber->expects($this->never())
            ->method($this->anything());

        $scrubberFacade = new ScrubberFacade([$scrubber]);

        $data = ['key' => 'value'];
        $actualResult = $scrubberFacade->scrub($data, 'xxx');

        $this->assertEquals($data, $actualResult);
    }
}
