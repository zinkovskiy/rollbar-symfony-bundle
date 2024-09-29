<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Service;

use Ant\RollbarSymfonyBundle\Service\PersonProvider\PersonProviderFacade;
use Monolog\Handler\RollbarHandler;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Kernel;

final class RollbarHandlerFactory
{
    /** @param array<string, mixed> $config */
    public function __construct(
        string $environment,
        array $config,
        private readonly RollbarWrapper $rollbar,
        PersonProviderFacade $personProviderFacade,
    ) {
        $config['environment'] = $environment;
        $config['framework'] = 'Symfony '.Kernel::VERSION;
        $config['person_fn'] = $personProviderFacade;

        $rollbar->init($config);
    }

    public function createRollbarHandler(): RollbarHandler
    {
        return new RollbarHandler($this->rollbar->getLogger(), LogLevel::ERROR);
    }
}
