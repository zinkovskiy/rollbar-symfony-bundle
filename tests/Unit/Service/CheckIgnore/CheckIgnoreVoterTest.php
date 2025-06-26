<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\CheckIgnore;

use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;
use Rollbar\Payload\Payload;
use SFErTrack\RollbarSymfonyBundle\Service\CheckIgnore\CheckIgnoreVoter;
use SFErTrack\RollbarSymfonyBundle\Tests\Unit\Stub\IgnoredException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class CheckIgnoreVoterTest extends TestCase
{
    /** @return array<int, array<int, mixed>> */
    public static function dataProvider(): array
    {
        return [
            [new AccessDeniedHttpException(), true],
            [new MethodNotAllowedHttpException([Request::METHOD_GET, Request::METHOD_POST]), true],
            [new NotFoundHttpException(), true],
            [new Exception(), false],
            [new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY), false],
            [new LogicException(), false],
            [new IgnoredException(), true],
        ];
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function checkVoter(Throwable $exception, bool $expected): void
    {
        $payload = $this->createMock(Payload::class);

        $res = (new CheckIgnoreVoter())
            ->shouldIgnore(true, $exception, $payload);

        $this->assertEquals($expected, $res);
    }
}
