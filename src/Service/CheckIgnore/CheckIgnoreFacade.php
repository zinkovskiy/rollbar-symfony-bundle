<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Service\CheckIgnore;

use Rollbar\Payload\Payload;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class CheckIgnoreFacade
{
    /** @param array<int, mixed> $checkIgnoreVoters each service of the array should implement CheckIgnoreVoterInterface */
    public function __construct(
        #[AutowireIterator('rollbar.check_ignore_voter')]
        private readonly iterable $checkIgnoreVoters,
    ) {}

    public function __invoke(bool $isUncaught, mixed $toLog, Payload $payload): bool
    {
        foreach ($this->checkIgnoreVoters as $checkIgnoreVoter) {
            /** @var CheckIgnoreVoterInterface $checkIgnoreVoter */
            if ($checkIgnoreVoter->shouldIgnore($isUncaught, $toLog, $payload)) {
                return true;
            }
        }

        return false;
    }
}
