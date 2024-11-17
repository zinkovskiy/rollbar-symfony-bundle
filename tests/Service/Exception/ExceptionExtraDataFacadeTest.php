<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Service\Exception;

use SFErTrack\RollbarSymfonyBundle\Service\CheckIgnore\CheckIgnoreVoterInterface;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataFacade;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataInterface;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataProviderInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ExceptionExtraDataFacadeTest extends TestCase
{
    /** @test */
    public function handleExceptionWithExtraDataInterface(): void
    {
        $extraData = ['customer' => 82348];

        $exceptionExtraDataProviderMock = $this->getMockBuilder(ExceptionExtraDataProviderInterface::class)
            ->onlyMethods(['supports', 'getExtraData'])
            ->getMock();

        $exceptionExtraDataProviderMock->expects($this->once())
            ->method('supports')
            ->willReturn(true);

        $exceptionExtraDataProviderMock->expects($this->once())
            ->method('getExtraData')
            ->willReturn($extraData);

        $exceptionMock = $this->createMock(ExceptionExtraDataInterface::class);

        $service = new ExceptionExtraDataFacade([$exceptionExtraDataProviderMock]);
        $actualResult = $service($exceptionMock, []);

        $this->assertSame($extraData, $actualResult);
    }

    /** @test */
    public function handleThrowable(): void
    {
        $exceptionExtraDataProviderMock = $this->getMockBuilder(ExceptionExtraDataProviderInterface::class)
            ->onlyMethods(['supports', 'getExtraData'])
            ->getMock();

        $exceptionExtraDataProviderMock->expects($this->once())
            ->method('supports')
            ->willReturn(false);

        $exceptionExtraDataProviderMock->expects($this->never())
            ->method('getExtraData');

        $exceptionMock = $this->createMock(Throwable::class);

        $service = new ExceptionExtraDataFacade([$exceptionExtraDataProviderMock]);
        $actualResult = $service($exceptionMock, []);

        $this->assertSame([], $actualResult);
    }

    /** @test */
    public function handleExceptionWithBrokenProvider(): void
    {
        $exceptionExtraDataProviderMock = $this->getMockBuilder(ExceptionExtraDataProviderInterface::class)
            ->onlyMethods(['supports', 'getExtraData'])
            ->getMock();

        $exceptionExtraDataProviderMock->expects($this->once())
            ->method('supports')
            ->willThrowException(new Exception());

        $exceptionExtraDataProviderMock->expects($this->never())
            ->method('getExtraData');

        $exceptionMock = $this->createMock(Throwable::class);

        $service = new ExceptionExtraDataFacade([$exceptionExtraDataProviderMock]);
        $actualResult = $service($exceptionMock, []);

        $this->assertSame([], $actualResult);
    }

    /** @test */
    public function handleExceptionWithUnsupportedProvider(): void
    {
        $otherExtraDataProviderMock = $this->createMock(CheckIgnoreVoterInterface::class);
        $otherExtraDataProviderMock->expects($this->never())
            ->method($this->anything());

        $exceptionMock = $this->createMock(ExceptionExtraDataInterface::class);

        $service = new ExceptionExtraDataFacade([$otherExtraDataProviderMock]);
        $actualResult = $service($exceptionMock, []);

        $this->assertSame([], $actualResult);
    }

    /** @test */
    public function handleExceptionWithSeveralProviders(): void
    {
        $extraData = ['customer' => 82348];

        $exceptionExtraDataProviderMock1 = $this->getMockBuilder(ExceptionExtraDataProviderInterface::class)
            ->onlyMethods(['supports', 'getExtraData'])
            ->getMock();

        $exceptionExtraDataProviderMock1->expects($this->once())
            ->method('supports')
            ->willReturn(true);

        $exceptionExtraDataProviderMock1->expects($this->once())
            ->method('getExtraData')
            ->willReturn($extraData);

        $exceptionExtraDataProviderMock2 = $this->createMock(ExceptionExtraDataProviderInterface::class);
        $exceptionExtraDataProviderMock2->expects($this->never())
            ->method($this->anything());

        $exceptionMock = $this->createMock(ExceptionExtraDataInterface::class);

        $service = new ExceptionExtraDataFacade([$exceptionExtraDataProviderMock1, $exceptionExtraDataProviderMock2]);
        $actualResult = $service($exceptionMock, []);

        $this->assertSame($extraData, $actualResult);
    }


    /** @test */
    public function handleUnsupportedException(): void
    {
        $exceptionExtraDataProviderMock1 = $this->getMockBuilder(ExceptionExtraDataProviderInterface::class)
            ->onlyMethods(['supports', 'getExtraData'])
            ->getMock();

        $exceptionExtraDataProviderMock1->expects($this->once())
            ->method('supports')
            ->willReturn(false);

        $exceptionExtraDataProviderMock1->expects($this->never())
            ->method('getExtraData');

        $exceptionExtraDataProviderMock2 = $this->getMockBuilder(ExceptionExtraDataProviderInterface::class)
            ->onlyMethods(['supports', 'getExtraData'])
            ->getMock();

        $exceptionExtraDataProviderMock2->expects($this->once())
            ->method('supports')
            ->willReturn(false);

        $exceptionExtraDataProviderMock2->expects($this->never())
            ->method('getExtraData');

        $exceptionMock = $this->createMock(ExceptionExtraDataInterface::class);

        $service = new ExceptionExtraDataFacade([$exceptionExtraDataProviderMock1, $exceptionExtraDataProviderMock2]);
        $actualResult = $service($exceptionMock, []);

        $this->assertSame([], $actualResult);
    }
}
