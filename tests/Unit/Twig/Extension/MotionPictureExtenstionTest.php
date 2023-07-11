<?php

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use App\Twig\Extension\MotionPictureExtenstion;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

/**
 * @coversDefaultClass \App\Twig\Extension\MotionPictureExtenstion
 * @testdox モーションピクチャーのサービスに関するTwig拡張機能
 */
final class MotionPictureExtenstionTest extends TestCase
{
    /**
     * @param array{'api_endpoint'?: string, 'api_project_id'?: string} $params
     */
    private function factoryMotionPictureExtenstion(array $params = []): MotionPictureExtenstion
    {
        $params['api_endpoint']   ??= 'https://example.com';
        $params['api_project_id'] ??= 'hogefuga';

        return new MotionPictureExtenstion($params);
    }

    /**
     * @covers ::getFunctions
     * @dataProvider functionNameDataProvider
     * @test
     */
    public function 決まった名称のtwigヘルパー関数が含まれる(string $name): void
    {
        // Arrange
        $sut = $this->factoryMotionPictureExtenstion();

        // Act
        $functions = $sut->getFunctions();

        // Assert
        $functionNames = [];

        foreach ($functions as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
            $functionNames[] = $function->getName();
        }

        $this->assertContains($name, $functionNames);
    }

    /**
     * @return array<array{string}>
     */
    public function functionNameDataProvider(): array
    {
        return [
            ['mp_api_endpoint'],
            ['mp_api_project_id'],
        ];
    }

    /**
     * @covers ::getApiEndpoint
     * @test
     */
    public function APIのエンドポイントを取得する(): void
    {
        // Arrange
        $sut = $this->factoryMotionPictureExtenstion(['api_endpoint' => 'https://api.example.com']);

        // Act
        $result = $sut->getApiEndpoint();

        // Assert
        $this->assertSame('https://api.example.com', $result);
    }

    /**
     * @covers ::getApiProjectId
     * @test
     */
    public function APIのプロジェクトIDを取得する(): void
    {
        // Arrange
        $sut = $this->factoryMotionPictureExtenstion(['api_project_id' => 'hoge_puroject']);

        // Act
        $result = $sut->getApiProjectId();

        // Assert
        $this->assertSame('hoge_puroject', $result);
    }
}
