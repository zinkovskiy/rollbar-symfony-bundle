<?php

declare(strict_types=1);

namespace Ant\RollbarSymfonyBundle\Tests\Service\PersonProvider;

use Ant\RollbarSymfonyBundle\Service\PersonProvider\PersonProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PersonProviderTest extends TestCase
{
    private TokenStorageInterface $tokenStorage;
    private PersonProvider $personProvider;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMock();

        $this->personProvider = new PersonProvider($this->tokenStorage);
    }

    /** @test */
    public function unauthenticatedUser(): void
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $userInfo = $this->personProvider->getUserInfo();

        $this->assertNull($userInfo);
    }

    /** @test */
    public function authenticatedUser(): void
    {
        $user = $this->createMock(UserInterface::class);
        $token = $this->createConfiguredMock(TokenInterface::class, ['getUser' => $user]);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $userInfo = $this->personProvider->getUserInfo();

        $this->assertEquals($user, $userInfo);
    }

    /** @test */
    public function impersonatedUser(): void
    {
        $impersonator = $this->createMock(UserInterface::class);
        $originalToken = $this->createConfiguredMock(TokenInterface::class, ['getUser' => $impersonator]);

        $userId = '134070';
        $user = $this->createConfiguredMock(UserInterface::class, ['getUserIdentifier' => $userId]);
        $token = $this->createConfiguredMock(
            SwitchUserToken::class,
            ['getUser' => $user, 'getOriginalToken' => $originalToken]
        );

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $userInfo = $this->personProvider->getUserInfo();

        $this->assertArrayHasKey('id', $userInfo);
        $this->assertSame($userId, $userInfo['id'] ?? null);

        $this->assertArrayHasKey('user', $userInfo);
        $this->assertSame($user, $userInfo['user'] ?? null);

        $this->assertArrayHasKey('impersonator', $userInfo);
        $this->assertSame($impersonator, $userInfo['impersonator'] ?? null);
    }
}
