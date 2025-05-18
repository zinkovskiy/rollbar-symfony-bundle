<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

interface ScrubberInterface
{
    /**
     * @param array<string, mixed> $data array to scrub
     * @return array<string, mixed> scrubbed data
     */
    public function scrub(array &$data, string $replacement): array;
}
