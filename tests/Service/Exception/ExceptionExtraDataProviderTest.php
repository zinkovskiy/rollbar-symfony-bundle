<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Tests\Service\Exception;

use Ant\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataInterface;
use Ant\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataProvider;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ExceptionExtraDataProviderTest extends TestCase
{
    private ExceptionExtraDataProvider $extraDataProvider;

    protected function setUp(): void
    {
        $this->extraDataProvider = new ExceptionExtraDataProvider();
    }

    /** @test */
    public function supportsExceptionExtraDataInterface(): void
    {
        $extraDataExceptionMock = $this->createConfiguredMock(
            ExceptionExtraDataInterface::class,
            [
                'getExtraData' => ['customer' => 57302],
            ]
        );

        $actualResult = $this->extraDataProvider->supports($extraDataExceptionMock, []);

        $this->assertSame(true, $actualResult);
    }

    /** @test */
    public function supportsException(): void
    {
        $actualResult = $this->extraDataProvider->supports(new Exception(), []);

        $this->assertSame(false, $actualResult);
    }

    /** @test */
    public function getExtraData(): void
    {
        $expectedExtraData = ['customer' => 57302];

        $extraDataExceptionMock = $this->createConfiguredMock(
            ExceptionExtraDataInterface::class,
            [
                'getExtraData' => $expectedExtraData,
            ]
        );

        $actualResult = $this->extraDataProvider->getExtraData($extraDataExceptionMock, []);
        $this->assertSame($expectedExtraData, $actualResult);
    }

    /** @test */
    public function getExtraDataMistakeCall(): void
    {
        $throwable = $this->createMock(Throwable::class);

        $actualResult = $this->extraDataProvider->getExtraData($throwable, []);
        $this->assertSame([], $actualResult);
    }
}
