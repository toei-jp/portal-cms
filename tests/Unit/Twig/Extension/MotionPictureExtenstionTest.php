<?php

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use App\Twig\Extension\MotionPictureExtenstion;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class MotionPictureExtenstionTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&MotionPictureExtenstion
     */
    protected function createTargetMock()
    {
        return Mockery::mock(MotionPictureExtenstion::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(MotionPictureExtenstion::class);
    }

    /**
     * @test
     */
    public function testConstruct(): void
    {
        $targetMock = $this->createTargetMock();
        $settings   = ['foo' => 'bar'];

        $targetRef = $this->createTargetReflection();

        // execute constructor
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
     * @test
     */
    public function testGetApiEndpoint(): void
    {
        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();
        $settings = ['api_endpoint' => 'example.com/api'];

        $targetRef = $this->createTargetReflection();

        $settingsPropertyRef = $targetRef->getProperty('settings');
        $settingsPropertyRef->setAccessible(true);
        $settingsPropertyRef->setValue($targetMock, $settings);

        $this->assertEquals($settings['api_endpoint'], $targetMock->getApiEndpoint());
    }
}
