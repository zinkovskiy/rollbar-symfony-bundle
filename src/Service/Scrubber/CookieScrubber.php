<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

final class CookieScrubber implements ScrubberInterface
{
    /** @param array<int, string> $scrubFields list of cookie names which values should be scrubbed */
    public function __construct(private readonly array $scrubFields) {}

    /**
     * @param array<string, mixed> $data array to scrub
     * @return array<string, mixed> scrubbed data
     */
    public function scrub(array &$data, string $replacement): array
    {
        if (empty($this->scrubFields)) {
            return $data;
        }

        $cookieString = $data['request']['headers']['Cookie'] ?? null;
        if (!$cookieString) {
            return $data;
        }

        foreach ($this->scrubFields as $scrubField) {
            $pattern = '/(' . preg_quote($scrubField, '/') . '=)([^;]*)/';
            $cookieString = preg_replace($pattern, '$1' . $replacement, $cookieString);
        }

        $data['request']['headers']['Cookie'] = $cookieString;

        return $data;
    }
}
