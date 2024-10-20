<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Service\Exception;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Throwable;

final class ExceptionExtraDataFacade
{
    /** @param array<int, mixed> $extraDataProviders each service of the array should implement ExceptionExtraDataInterface */
    public function __construct(
        #[AutowireIterator('rollbar.exception_extra_data_provider')]
        private readonly iterable $extraDataProviders,
    ) {}

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    public function __invoke(Throwable|string $toLog, ?array $context): array
    {
        foreach ($this->extraDataProviders as $extraDataProvider) {
            try {
                if (!$extraDataProvider instanceof ExceptionExtraDataProviderInterface) {
                    continue;
                }

                if ($extraDataProvider->supports($toLog, $context)) {
                    return $extraDataProvider->getExtraData($toLog, $context);
                }
            } catch (Throwable) {
                // Do nothing
            }
        }

        return [];
    }
}
