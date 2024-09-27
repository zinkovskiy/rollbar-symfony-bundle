<?php

namespace Tests\Fixtures\App;

use Ant\RollbarSymfonyBundle\RollbarSymfonyBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new MonologBundle(),
            new SecurityBundle(),
            new RollbarSymfonyBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getCacheDir(): string
    {
        return realpath(__DIR__.'/../../../').'/var/'.$this->getEnvironment().'/cache';
    }

    public function getLogDir(): string
    {
        return realpath(__DIR__.'/../../../').'/var/'.$this->getEnvironment().'/logs';
    }
}
