<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BundleConfigurationTest extends KernelTestCase
{
    /** @test */
    public function loadExtension(): void
    {
        self::bootKernel();

        $actualConfig = self::$kernel->getContainer()->getParameter('rollbar.config');
        $this->assertNotEmpty($actualConfig);

        $rollbarDefaults = json_decode(
            file_get_contents(
                dirname(__FILE__).'/data/rollbarConfigDefaultValues.json'
            ),
            true,
        );

        $this->assertEquals($rollbarDefaults, $actualConfig);
    }
}
