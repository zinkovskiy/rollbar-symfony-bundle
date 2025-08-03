<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

use Rollbar\Scrubber;

class DefaultScrubber extends Scrubber implements ScrubberInterface
{
    /**
     * @param array<int, string> $scrubFields list of key names which values should be scrubbed
     * @param array<int, string> $safelist list of key names which values should never be scrubbed
     */
    public function __construct(array $scrubFields, array $safelist, private readonly bool $scrubEnvVariables)
    {
        parent::__construct([
            'scrubFields' => $scrubFields,
            'scrubSafelist' => array_merge($safelist, ['APP_ENV']),
        ]);
    }

    /**
     * @param array<string, mixed> $data array to scrub
     * @return array<string, mixed> scrubbed data
     */
    public function scrub(array &$data, string $replacement = '********', string $path = ''): array
    {
        if ($this->scrubEnvVariables) {
            $symfonyDotenvVars = isset($_ENV['SYMFONY_DOTENV_VARS']) ? explode(',', $_ENV['SYMFONY_DOTENV_VARS']) : null;
            $this->scrubFields = array_diff(
                array_merge(
                    $this->scrubFields,
                    $symfonyDotenvVars ?? array_keys($_ENV)
                ),
                $this->safelist
            );
        }

        return parent::scrub($data, $replacement, $path);
    }
}
