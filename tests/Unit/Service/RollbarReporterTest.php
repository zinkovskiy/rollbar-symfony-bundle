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
            ->with(Level::ERROR, $exceptionMessage, []);

        $this->rollbarReporter->reportError($exception);
    }

    /** @test */
    public function reportRegularExceptionWithAdditionalExtraData(): void
    {
        $exceptionMessage = 'Awesome exception message';
        $exception = new Exception($exceptionMessage);
        $extraData = ['key' => 'value'];

        $this->rollbarLogger->expects($this->once())
            ->method('log')
            ->with(Level::ERROR, $exceptionMessage, ['custom_data_method_context' => $extraData]);

        $this->rollbarReporter->reportError($exception, $extraData);
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

    public static function reportingLevelsDataProvider(): array
    {
        return [
            [Level::EMERGENCY],
            [Level::ALERT],
            [Level::CRITICAL],
            [Level::ERROR],
            [Level::WARNING],
            [Level::NOTICE],
            [Level::INFO],
            [Level::DEBUG],
        ];
    }

    /**
     * @test
     * @dataProvider reportingLevelsDataProvider
     */
    public function checkReportingLevel(string $level): void
    {
        $exceptionMessage = 'Awesome exception message';
        $exception = new Exception($exceptionMessage);

        $this->rollbarLogger->expects($this->once())
            ->method('log')
            ->with($level, $exceptionMessage, []);

        match ($level) {
            Level::EMERGENCY => $this->rollbarReporter->reportEmergency($exception),
            Level::ALERT => $this->rollbarReporter->reportAlert($exception),
            Level::CRITICAL => $this->rollbarReporter->reportCritical($exception),
            Level::ERROR => $this->rollbarReporter->reportError($exception),
            Level::WARNING => $this->rollbarReporter->reportWarning($exception),
            Level::NOTICE => $this->rollbarReporter->reportNotice($exception),
            Level::INFO => $this->rollbarReporter->reportInfo($exception),
            Level::DEBUG => $this->rollbarReporter->reportDebug($exception),
        };
    }
}
