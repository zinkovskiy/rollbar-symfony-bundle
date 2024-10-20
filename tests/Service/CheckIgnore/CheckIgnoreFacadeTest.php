<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Tests\Service\CheckIgnore;

use Ant\RollbarSymfonyBundle\Service\CheckIgnore\CheckIgnoreFacade;
use Ant\RollbarSymfonyBundle\Service\CheckIgnore\CheckIgnoreVoterInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Rollbar\Payload\Payload;

final class CheckIgnoreFacadeTest extends TestCase
{
    private const NUMBER_OF_CALLS = 'number_of_calls';
    private const CALL_RESULT = 'result';

    /** @return array<int, array<int, array<int, array<string, bool|int>>>> */
    public static function checkIgnoreVoters(): array
    {
        return [
            [
                [
                    [self::NUMBER_OF_CALLS => 1, self::CALL_RESULT => false],
                    [self::NUMBER_OF_CALLS => 1, self::CALL_RESULT => true],
                ],
            ],
            [
                [
                    [self::NUMBER_OF_CALLS => 1, self::CALL_RESULT => true],
                    [self::NUMBER_OF_CALLS => 0],
                ],
            ],
            [
                [
                    [self::NUMBER_OF_CALLS => 1, self::CALL_RESULT => false],
                    [self::NUMBER_OF_CALLS => 1, self::CALL_RESULT => false],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider checkIgnoreVoters
     * @param array<int, array<string, mixed>> $checkIgnoreVotersData
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function checkFacade(array $checkIgnoreVotersData): void
    {
        $checkIgnoreVoters = [];
        foreach ($checkIgnoreVotersData as $data) {
            $mock = $this->getMockBuilder(CheckIgnoreVoterInterface::class)
                ->onlyMethods(['shouldIgnore'])
                ->getMock();

            $numberOfCalls = $data[self::NUMBER_OF_CALLS];
            if ($numberOfCalls !== 0) {
                $mock->expects($this->exactly($numberOfCalls))
                    ->method('shouldIgnore')
                    ->willReturn($data[self::CALL_RESULT]);
            } else {
                $mock->expects($this->never())
                    ->method('shouldIgnore');
            }

            $checkIgnoreVoters[] = $mock;
        }

        $checkIgnoreFacade = new CheckIgnoreFacade($checkIgnoreVoters);

        $payload = $this->createMock(Payload::class);

        $checkIgnoreFacade(false, new Exception(), $payload);
    }
}
