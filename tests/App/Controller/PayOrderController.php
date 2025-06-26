<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Controller;

use SFErTrack\RollbarSymfonyBundle\Tests\App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PayOrderController extends AbstractController
{
    #[Route('/pay-order/{orderId}', methods: ['GET'])]
    public function throwException(string $orderId, OrderService $orderService): Response
    {
        $orderService->payOrder($orderId);

        return new Response();
    }
}
