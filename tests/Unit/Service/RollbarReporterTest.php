<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rollbar\Payload\Level;
use Rollbar\RollbarLogger;
use SFErTrack\RollbarSymfonyBundle\Service\RollbarReporter;
use SFErTrack\RollbarSymfonyBundle\Tests\Unit\Stub\ExtraDataException;

class RollbarReporterTest extends TestCase
{
    private RollbarLogger&MockObject $rollbarLogger;

    private RollbarReporter $rollbarReporter;

    protected function setUp(): void
    {
        $this->rollbarLogger = $this->getMockBuilder(RollbarLogger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['log'])
            ->getMock();

        $this->rollbarReporter = new RollbarReporter($this->rollbarLogger);
    }

    /** @test */
    public function reportRegularException(): void
    {
        $exceptionMessage = 'Awesome exception message';
        $exception = new Exception($exceptionMessage);

        $this->rollbarLogger->expects($this->once())
            ->method('log')
            ->with(Level::EMERGENCY, $exceptionMessage, []);

        $this->rollbarReporter->reportError($exception, level: Level::EMERGENCY);
    }

    /** @test */
    public function reportRegularExceptionWithAdditionalExtraData(): void
    {
        $exceptionMessage = 'Awesome exception message';
        $exception = new Exception($exceptionMessage);
        $extraData = ['key' => 'value'];

        $this->rollbarLogger->expects($this->once())
            ->method('log')
            ->with(Level::EMERGENCY, $exceptionMessage, ['custom_data_method_context' => $extraData]);

        $this->rollbarReporter->reportError($exception, $extraData, level: Level::EMERGENCY);
    }

    /** @test */
    public function reportExtraDataException(): void
    {
        $exceptionMessage = 'Awesome exception message';
        $exception = new ExtraDataException($exceptionMessage);

        $this->rollbarLogger->expects($this->once())
            ->method('log')
            ->with(Level::ERROR, $exceptionMessage, ['custom_data_method_context' => ['key' => 'value']]);

        $this->rollbarReporter->reportError($exception);
    }

    /** @test */
    public function reportExtraDataExceptionWithAdditionalExtraData(): void
    {
        $exceptionMessage = 'Awesome exception message';
        $exception = new ExtraDataException($exceptionMessage);
        $extraData = ['additional-key' => 'value'];

        $this->rollbarLogger->expects($this->once())
            ->method('log')
            ->with(
                Level::ERROR,
                $exceptionMessage,
                ['custom_data_method_context' => ['key' => 'value', 'additional-key' => 'value']]
            );

        $this->rollbarReporter->reportError($exception, $extraData);
    }

    /** @test */
    public function reportExtraDataExceptionWithAdditionalExtraDataExistingKey(): void
    {
        $exceptionMessage = 'Awesome exception message';
        $exception = new ExtraDataException($exceptionMessage);
        $extraData = ['key' => 'value of repeated key'];

        $this->rollbarLogger->expects($this->once())
            ->method('log')
            ->with(
                Level::ERROR,
                $exceptionMessage,
                ['custom_data_method_context' => ['key' => 'value']]
            );

        $this->rollbarReporter->reportError($exception, $extraData);
    }
}
