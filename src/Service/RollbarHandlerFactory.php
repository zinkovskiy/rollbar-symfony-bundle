<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service;

use SFErTrack\RollbarSymfonyBundle\Service\CheckIgnore\CheckIgnoreFacade;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataFacade;
use SFErTrack\RollbarSymfonyBundle\Service\PersonProvider\PersonProviderFacade;
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
        CheckIgnoreFacade $checkIgnoreFacade,
        ExceptionExtraDataFacade $exceptionExtraDataFacade,
    ) {
        $config['environment'] = $environment;
        $config['framework'] = 'Symfony '.Kernel::VERSION;
        $config['person_fn'] = $personProviderFacade;
        $config['check_ignore'] = $checkIgnoreFacade;
        $config['custom_data_method'] = $exceptionExtraDataFacade;

        $rollbar->init($config);
    }

    public function createRollbarHandler(): RollbarHandler
    {
        return new RollbarHandler($this->rollbar->getLogger(), LogLevel::ERROR);
    }
}
