<?php

/**
 * SessionManagerTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Session;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Toei\PortalAdmin\Session\SessionManager;

/**
 * Session Manager test
 */
final class SessionManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * test getContainer
     *
     * @test
     * @return void
     */
    public function testGetContainer()
    {
        $sessionManagerMock = Mockery::mock(SessionManager::class)
            ->makePartial();

        $name   = 'test';
        $result = $sessionManagerMock->getContainer($name);

        $sessionManagerRef = new \ReflectionClass(SessionManager::class);

        $containersPropertyRef = $sessionManagerRef->getProperty('containers');
        $containersPropertyRef->setAccessible(true);
        $containers = $containersPropertyRef->getValue($sessionManagerMock);
        $container  = $containers[$name];

        $this->assertEquals($name, $container->getName());
        $this->assertEquals($container, $result);
    }
}
