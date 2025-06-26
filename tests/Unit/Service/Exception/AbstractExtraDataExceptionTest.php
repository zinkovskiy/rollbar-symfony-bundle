<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\Exception;

use PHPUnit\Framework\TestCase;
use SFErTrack\RollbarSymfonyBundle\Tests\Unit\Stub\ExpandedExtraDataException;

final class AbstractExtraDataExceptionTest extends TestCase
{
    /** @test */
    public function checkExpandedExtraDataException(): void
    {
        $exception = new ExpandedExtraDataException(['key' => 'value']);
        $exception->addExtraDataItem('other-key', 'other-value');

        $extraData = $exception->getExtraData();

        $this->assertSame(
            [
                'other-key' => 'other-value',
                'key' => 'value',
            ],
            $extraData
        );
    }
}
