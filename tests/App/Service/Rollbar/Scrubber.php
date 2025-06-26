<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Service\Rollbar;

use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\ScrubberInterface;

final class Scrubber implements ScrubberInterface
{
    private int $countCalls = 0;

    public function scrub(array &$data, string $replacement): array
    {
        $this->countCalls++;

        return $data;
    }

    public function getCountCalls(): int
    {
        return $this->countCalls;
    }
}
