<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Scrubber;

use Exception;
use PHPUnit\Framework\TestCase;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\ScrubberFacade;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\ScrubberInterface;
use SFErTrack\RollbarSymfonyBundle\Tests\Unit\Stub\ServiceInterface;

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

    /** @test */
    public function scrubberWithCorrectInterfaces(): void
    {
        $scrubber1 = $this->getMockBuilder(ScrubberInterface::class)
            ->onlyMethods(['scrub'])
            ->getMock();

        $scrubber2 = $this->getMockBuilder(\Rollbar\ScrubberInterface::class)
            ->onlyMethods(['scrub'])
            ->getMock();

        $scrubber1->expects($this->once())
            ->method('scrub');

        $scrubber2->expects($this->once())
            ->method('scrub');

        $scrubberFacade = new ScrubberFacade([$scrubber1, $scrubber2]);

        $data = ['key' => 'value'];
        $actualResult = $scrubberFacade->scrub($data, 'xxx');

        $this->assertEquals($data, $actualResult);
    }
}
