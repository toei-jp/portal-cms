<?php

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\Mock;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Base test case
 */
abstract class BaseTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create Target mock
     *
     * @return MockInterface|LegacyMockInterface|AbstractExtension
     */
    abstract protected function createTargetMock();

    /**
     * test getFunctions
     *
     * @test
     *
     * @return void
     */
    public function testGetFunctions()
    {
        /** @var Mock|AbstractExtension $targetMock */
        $targetMock = $this->createTargetMock()
            ->makePartial();

        $functions = $targetMock->getFunctions();

        $this->assertIsArray($functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
