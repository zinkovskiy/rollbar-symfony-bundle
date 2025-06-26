<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\App\Controller;

use Exception;
use SFErTrack\RollbarSymfonyBundle\Tests\App\Exception\TestExceptionMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/throw-exception', methods: ['GET'])]
    public function throwException(): Response
    {
        throw new Exception('Test exception');
    }

    #[Route('/throw-user-friendly-exception', methods: ['GET'])]
    public function throwUserFriendlyException(): Response
    {
        throw new TestExceptionMessage();
    }
}
