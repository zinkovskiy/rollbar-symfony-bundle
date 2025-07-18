<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle;

use Monolog\Handler\RollbarHandler;
use Rollbar\Config;
use Rollbar\Defaults;
use Rollbar\Rollbar;
use Rollbar\RollbarLogger;
use Rollbar\Scrubber;
use SFErTrack\RollbarSymfonyBundle\Service\CheckIgnore\CheckIgnoreVoterInterface;
use SFErTrack\RollbarSymfonyBundle\Service\Exception\ExceptionExtraDataProviderInterface;
use SFErTrack\RollbarSymfonyBundle\Service\PersonProvider\PersonProvider;
use SFErTrack\RollbarSymfonyBundle\Service\PersonProvider\PersonProviderInterface;
use SFErTrack\RollbarSymfonyBundle\Service\Scrubber\ScrubberInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Throwable;

final class RollbarSymfonyBundle extends AbstractBundle
{
    public function boot(): void
    {
        /** @var RollbarLogger $rollbarLogger */
        $rollbarLogger = $this->container->get(RollbarLogger::class);
        Rollbar::init($rollbarLogger, false, false, false);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $configOptions = Config::listOptions();
        $rollbarDefaults = Defaults::get();

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $definition->rootNode();
        $rollbarConfigNodeChildren = $rootNode->children();

        foreach ($configOptions as $option) {
            $method = match ($option) {
                'branch' => 'gitBranch',
                default => $option,
            };

            try {
                $defaultValue = $rollbarDefaults->fromSnakeCase($method);
            } catch (Throwable) {
                $defaultValue = null;
            }

            if (is_array($defaultValue)) {
                $rollbarConfigNodeChildren
                    ->arrayNode($option)
                    ->scalarPrototype()->end()
                    ->defaultValue($defaultValue)
                    ->end();
            } else {
                $rollbarConfigNodeChildren
                    ->scalarNode($option)
                    ->defaultValue($defaultValue)
                    ->end();
            }
        }

        $rollbarConfigNodeChildren->arrayNode('scrub_cookie_fields')
            ->scalarPrototype()
            ->end()
            ->defaultValue([])
            ->end();
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('monolog', [
            'handlers' => [
                'rollbar' => [
                    'type' => 'service',
                    'id' => RollbarHandler::class,
                ],
            ],
        ]);

        if ($builder->hasExtension('security')) {
            $container->services()
                ->set(PersonProvider::class)
                ->autoconfigure()
                ->autowire()
                ->tag('rollbar.person_provider', ['priority' => -1]);
        }
    }

    /** @param array<string, mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        $container->parameters()->set('rollbar.config', $config);
        $container->parameters()->set('rollbar.config.scrubber_cookie_fields', $config['scrub_cookie_fields'] ?? []);

        // I do not why, but AutoconfigureTag attribute and yaml annotation does not work in symfony 6.4 bundle
        $builder->registerForAutoconfiguration(PersonProviderInterface::class)
            ->addTag('rollbar.person_provider');

        $builder->registerForAutoconfiguration(CheckIgnoreVoterInterface::class)
            ->addTag('rollbar.check_ignore_voter');

        $builder->registerForAutoconfiguration(ExceptionExtraDataProviderInterface::class)
            ->addTag('rollbar.exception_extra_data_provider');

        $builder->registerForAutoconfiguration(ScrubberInterface::class)
            ->addTag('rollbar.scrubber');

        $container->services()->set(Scrubber::class)
            ->arg('$config', $config)
            ->tag('rollbar.scrubber');
    }
}
