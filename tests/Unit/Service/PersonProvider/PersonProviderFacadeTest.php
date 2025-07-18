<?php

declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Tests\Unit\Service\PersonProvider;

use Exception;
use PHPUnit\Framework\TestCase;
use SFErTrack\RollbarSymfonyBundle\Service\PersonProvider\PersonProviderFacade;
use SFErTrack\RollbarSymfonyBundle\Service\PersonProvider\PersonProviderInterface;
use SFErTrack\RollbarSymfonyBundle\Tests\Unit\Stub\ServiceInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PersonProviderFacadeTest extends TestCase
{
    private NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        $onlyMethods = ['supportsNormalization', 'normalize'];
        if (method_exists(NormalizerInterface::class, 'getSupportedTypes')) {
            $onlyMethods[] = 'getSupportedTypes';
        }

        $this->normalizer = $this->getMockBuilder(NormalizerInterface::class)
            ->onlyMethods($onlyMethods)
            ->getMock();
    }

    /** @test */
    public function supportedPersonProvider(): void
    {
        $expectedResult = [
            'id' => 43234532,
            'email' => 'test@example.com',
        ];

        $user = $this->createMock(UserInterface::class);

        $personProvider = $this->getMockBuilder(PersonProviderInterface::class)
            ->onlyMethods(['getUserInfo'])
            ->getMock();

        $personProvider->expects($this->once())
            ->method('getUserInfo')
            ->willReturn($user);

        $this->normalizer->expects($this->once())
            ->method('normalize')
            ->with($user)
            ->willReturn($expectedResult);

        $personProviderFacade = new PersonProviderFacade([$personProvider], $this->normalizer);

        $actualResult = $personProviderFacade();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /** @test */
    public function brokenPersonProvider(): void
    {
        $personProvider = $this->getMockBuilder(PersonProviderInterface::class)
            ->onlyMethods(['getUserInfo'])
            ->getMock();

        $personProvider->expects($this->once())
            ->method('getUserInfo')
            ->willThrowException(new Exception());

        $this->normalizer->expects($this->never())
            ->method($this->anything());

        $personProviderFacade = new PersonProviderFacade([$personProvider], $this->normalizer);

        $actualResult = $personProviderFacade();

        $this->assertNull($actualResult);
    }

    /** @test */
    public function personProviderWithoutInterface(): void
    {
        $personProvider = $this->createMock(ServiceInterface::class);

        $personProvider->expects($this->never())
            ->method($this->anything());

        $this->normalizer->expects($this->never())
            ->method($this->anything());

        $personProviderFacade = new PersonProviderFacade([$personProvider], $this->normalizer);

        $actualResult = $personProviderFacade();

        $this->assertNull($actualResult);
    }

    /** @test */
    public function differentPersonProviders(): void
    {
        $expectedResult = [
            'id' => 43234532,
            'email' => 'test@example.com',
        ];

        $user = $this->createMock(UserInterface::class);

        $unsupportedPersonProvider = $this->createMock(ServiceInterface::class);

        $unsupportedPersonProvider->expects($this->never())
            ->method($this->anything());

        $brokenPersonProvider = $this->createMock(PersonProviderInterface::class);

        $brokenPersonProvider->expects($this->once())
            ->method('getUserInfo')
            ->willThrowException(new Exception());

        $robustPersonProvider = $this->createMock(PersonProviderInterface::class);

        $robustPersonProvider->expects($this->once())
            ->method('getUserInfo')
            ->willReturn($user);

        $this->normalizer->expects($this->once())
            ->method('normalize')
            ->with($user)
            ->willReturn($expectedResult);

        $personProviderFacade = new PersonProviderFacade(
            [$unsupportedPersonProvider, $brokenPersonProvider, $robustPersonProvider],
            $this->normalizer
        );

        $actualResult = $personProviderFacade();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
