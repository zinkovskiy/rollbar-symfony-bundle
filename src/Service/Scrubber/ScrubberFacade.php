<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

use Rollbar\ScrubberInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Throwable;

final class ScrubberFacade implements ScrubberInterface
{
    /** @param array<int, mixed> $scrubbers each service of the array should implement ScrubberInterface */
    public function __construct(
        #[AutowireIterator('rollbar.scrubber')]
        private readonly iterable $scrubbers,
    ) {}

    /**
     * @param array<string, mixed> $data array to scrub
     * @return array<string, mixed> scrubbed data
     */
    public function scrub(array &$data, string $replacement = '********'): array
    {
        foreach ($this->scrubbers as $scrubber) {
            try {
                if (!$scrubber instanceof \SFErTrack\RollbarSymfonyBundle\Service\Scrubber\ScrubberInterface) {
                    continue;
                }

                $data = $scrubber->scrub($data, $replacement);
            } catch (Throwable) {
                // Do nothing
            }
        }

        return $data;
    }
}
