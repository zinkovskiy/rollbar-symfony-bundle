<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

interface ScrubberInterface {
    public function scrub(array &$data, string $replacement): array;
}
