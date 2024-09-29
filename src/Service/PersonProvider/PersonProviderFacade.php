<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Service\PersonProvider;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

final class PersonProviderFacade
{
    /** @param array<int, mixed> $personProviders each service of the array should implement PersonProviderInterface */
    public function __construct(
        #[AutowireIterator('rollbar.person_provider')]
        private readonly iterable $personProviders,
        private readonly NormalizerInterface $normalizer,
    ) {}

    /** @return array<string, mixed>|null */
    public function __invoke(): ?array
    {
        foreach ($this->personProviders as $personProvider) {
            try {
                if (!$personProvider instanceof PersonProviderInterface) {
                    continue;
                }

                $userInfo = $personProvider->getUserInfo();
                return $this->normalizer->normalize($userInfo);
            } catch (Throwable) {
                // Do nothing
            }
        }

        return null;
    }
}
