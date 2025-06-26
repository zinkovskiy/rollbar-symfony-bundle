<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Service;

use SFErTrack\RollbarSymfonyBundle\Service\Exception\AbstractExtraDataException;

final class OrderService
{
    public function __construct(private readonly ExternalServiceWrapper $externalServiceWrapper) {}

    public function payOrder(string $orderId): void
    {
        try {
            $this->externalServiceWrapper->sendRequest(['order_id' => $orderId]);
        } catch (AbstractExtraDataException $e) {
            // customer id is just an example: it can be obtained wherever you want
            // for example, from database by order id
            $customerId = '0197ac0b-2ec7-7fc7-a1af-778a8a3a92e1';
            $e->addExtraDataItem('customer_id', $customerId);

            throw $e;
        }
    }
}
