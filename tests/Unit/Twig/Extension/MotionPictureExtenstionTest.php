<?php

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use App\Twig\Extension\MotionPictureExtenstion;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

/**
 * @coversDefaultClass \App\Twig\Extension\MotionPictureExtenstion
 */
final class MotionPictureExtenstionTest extends TestCase
{
    protected const SETTINGS_API_ENDPOINT = 'https://api.example.com';

    protected MotionPictureExtenstion $extension;

    protected function setUp(): void
    {
        $settings = [
            'api_endpoint' => self::SETTINGS_API_ENDPOINT,
            'api_project_id' => 'project_example',
        ];

        $this->extension = new MotionPictureExtenstion($settings);
    }

    /**
     * @test
     */
    public function testGetFunctionsReturnArray(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertIsArray($functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }

    /**
     * @test
     */
    public function testGetFunctionsMatchFunctionName(): void
    {
        $expectedNames = [
            'mp_api_endpoint',
            'mp_api_project_id',
        ];

        $functions = $this->extension->getFunctions();
        $names     = array_map(static fn ($func): string => $func->getName(), $functions);

        foreach ($expectedNames as $expected) {
            $this->assertContains($expected, $names);
        }
    }

    /**
     * @test
     */
    public function testGetApiEndpoint(): void
    {
        $this->assertEquals(self::SETTINGS_API_ENDPOINT, $this->extension->getApiEndpoint());
    }

    /**
     * @test
     */
    public function testGetApiProjectId(): void
    {
        $this->assertEquals('project_example', $this->extension->getApiProjectId());
    }
}
