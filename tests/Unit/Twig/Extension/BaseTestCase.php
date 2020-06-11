<?php

/**
 * BaseTestCase.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
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
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|AbstractExtension
     */
    abstract protected function createTargetMock();

    /**
     * test getFunctions
     *
     * @test
     * @return void
     */
    public function testGetFunctions()
    {
        /** @var \Mockery\Mock|AbstractExtension $targetMock */
        $targetMock = $this->createTargetMock()
            ->makePartial();

        $functions = $targetMock->getFunctions();

        $this->assertIsArray($functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
