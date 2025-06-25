<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Service;

use Rollbar\RollbarLogger;
use SFErTrack\RollbarSymfonyBundle\Service\CheckIgnore\CheckIgnoreFacade;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataFacade;
use SFErTrack\RollbarSymfonyBundle\Service\PersonProvider\PersonProviderFacade;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\ScrubberFacade;
use Symfony\Component\HttpKernel\Kernel;

final class RollbarLoggerFactory
{
    /** @param array<string, mixed> $config */
    public function __construct(
        private array $config,
        string $env,
        string $projectDir,
        PersonProviderFacade $personProviderFacade,
        CheckIgnoreFacade $checkIgnoreFacade,
        ExceptionExtraDataFacade $exceptionExtraDataFacade,
        ScrubberFacade $scrubberFacade,
    ) {
        $this->config['environment'] = $env;
        $this->config['root'] = $projectDir;
        $this->config['framework'] = 'Symfony '.Kernel::VERSION;
        $this->config['person_fn'] = $personProviderFacade;
        $this->config['check_ignore'] = $checkIgnoreFacade;
        $this->config['custom_data_method'] = $exceptionExtraDataFacade;
        $this->config['scrubber'] = $scrubberFacade;
    }

    public function __invoke(): RollbarLogger
    {
        return new RollbarLogger($this->config);
    }
}
