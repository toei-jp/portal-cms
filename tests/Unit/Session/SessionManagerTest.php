<?php

declare(strict_types=1);

namespace Tests\Unit\Session;

use App\Session\SessionManager;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class SessionManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function testGetContainer(): void
    {
        $sessionManagerMock = Mockery::mock(SessionManager::class)
            ->makePartial();

        $name   = 'test';
        $result = $sessionManagerMock->getContainer($name);

        $sessionManagerRef = new ReflectionClass(SessionManager::class);

        $containersPropertyRef = $sessionManagerRef->getProperty('containers');
        $containersPropertyRef->setAccessible(true);
        $containers = $containersPropertyRef->getValue($sessionManagerMock);
        $container  = $containers[$name];

        $this->assertEquals($name, $container->getName());
        $this->assertEquals($container, $result);
    }
}
