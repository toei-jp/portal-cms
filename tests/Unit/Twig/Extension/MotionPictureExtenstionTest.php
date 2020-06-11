<?php

/**
 * MotionPictureExtenstionTest.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use Mockery;
use Toei\PortalAdmin\Twig\Extension\MotionPictureExtenstion;

/**
 * MotionPicture extension test
 */
final class MotionPictureExtenstionTest extends BaseTestCase
{
    /**
     * Create target mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|MotionPictureExtenstion
     */
    protected function createTargetMock()
    {
        return Mockery::mock(MotionPictureExtenstion::class);
    }

    /**
     * Create Target reflection
     *
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(MotionPictureExtenstion::class);
    }

    /**
     * test construct
     *
     * @test
     * @return void
     */
    public function testConstruct()
    {
        $targetMock = $this->createTargetMock();
        $settings = [
            'foo' => 'bar',
        ];

        // execute constructor
        $targetRef = $this->createTargetReflection();
        $constructorRef = $targetRef->getConstructor();
        $constructorRef->invoke($targetMock, $settings);

        // test property "settings"
        $settingsPropertyRef = $targetRef->getProperty('settings');
        $settingsPropertyRef->setAccessible(true);
        $this->assertEquals(
            $settings,
            $settingsPropertyRef->getValue($targetMock)
        );
    }

    /**
     * test getApiEndpoint
     *
     * @test
     * @return void
     */
    public function testGetApiEndpoint()
    {
        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();
        $settings = [
            'api_endpoint' => 'example.com/api',
        ];

        $targetRef = $this->createTargetReflection();
        $settingsPropertyRef = $targetRef->getProperty('settings');
        $settingsPropertyRef->setAccessible(true);
        $settingsPropertyRef->setValue($targetMock, $settings);

        $this->assertEquals($settings['api_endpoint'], $targetMock->getApiEndpoint());
    }
}