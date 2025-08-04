<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service\Scrubber;

class LocalVarsSecretsScrubber implements ScrubberInterface
{
    /** @param array<int, string> $safelist List of variable names that should never be scrubbed */
    public function __construct(
        private readonly array $safelist,
        private readonly bool $scrubEnvVariables
    ) {}

    public function scrub(array &$data, string $replacement): array
    {
        if ($this->scrubEnvVariables) {
            $secretValues = $this->getSecretValues();
            foreach ($data as &$value) {
                $this->scrubLocalVarsSecrets($value, $replacement, $secretValues);
            }
        }

        return $data;
    }

    /** @return array<int, string> List of env variable values that should be scrubbed */
    private function getSecretValues(): array
    {
        $scrubFields = array_diff(array_keys($_ENV), $this->safelist, ['APP_ENV']);

        $secretValues = [];
        foreach ($scrubFields as $scrubField) {
            if ($value = $_ENV[$scrubField] ?? null) {
                $secretValues[] = $value;
            }
        }

        return $secretValues;
    }

    /** @param array<int, string> $secretValues List of env variable values that should be scrubbed */
    private function scrubLocalVarsSecrets(mixed &$value, string $replacement, array $secretValues): void
    {
        if (is_array($value)) {
            foreach ($value as &$item) {
                $this->scrubLocalVarsSecrets($item, $replacement, $secretValues);
            }

            return;
        }

        if (in_array(strtolower((string)$value), $secretValues)) {
            $value = $replacement;
        }
    }
}
