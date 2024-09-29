<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Service\PersonProvider;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Checks whether to ignore exception or not.
 */
#[AutoconfigureTag('rollbar.person_provider')]
interface PersonProviderInterface
{
    public function getUserInfo(): mixed;
}
