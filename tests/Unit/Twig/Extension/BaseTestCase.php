<?php

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

abstract class BaseTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&AbstractExtension
     */
    abstract protected function createTargetMock();

    /**
     * @test
     */
    public function testGetFunctions(): void
    {
        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $functions = $targetMock->getFunctions();

        $this->assertIsArray($functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
