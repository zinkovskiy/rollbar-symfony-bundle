<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Service;

use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Rollbar\RollbarLogger;
use Stringable;

final class RollbarWrapper
{
    /** @param array<string, mixed> $config */
    public function init(array $config): void
    {
        Rollbar::init($config, false, false, false);
    }

    /** @param array<string, mixed> $customDataMethodContext */
    public function log(Level|string $level, string|Stringable $message, array $customDataMethodContext = []): void
    {
        Rollbar::log(
            $level,
            $message,
            [
                'custom_data_method_context' => $customDataMethodContext,
            ]
        );
    }

    public function getLogger(): RollbarLogger
    {
        return Rollbar::logger();
    }
}
