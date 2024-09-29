<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle;

use Rollbar\Config;
use Rollbar\Defaults;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Throwable;

final class RollbarSymfonyBundle extends AbstractBundle
{
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
    }

    /** @param array<string, mixed> $config */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        $container->parameters()->set('rollbar.config', $config);
    }
}
