<?php

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use App\Twig\Extension\AzureStorageExtension;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

/**
 * @coversDefaultClass \App\Twig\Extension\AzureStorageExtension
 */
final class AzureStorageExtensionTest extends TestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&BlobRestProxy
     */
    protected function createBlobRestProxyMock()
    {
        return Mockery::mock(BlobRestProxy::class);
    }

    /**
     * @test
     */
    public function testGetFunctionsReturnArray(): void
    {
        $extension = new AzureStorageExtension($this->createBlobRestProxyMock());

        $functions = $extension->getFunctions();

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
        $expectedNames = ['blob_url'];
        $extension     = new AzureStorageExtension($this->createBlobRestProxyMock());

        $functions = $extension->getFunctions();
        $names     = array_map(static fn ($func): string => $func->getName(), $functions);

        foreach ($expectedNames as $expected) {
            $this->assertContains($expected, $names);
        }
    }

    /**
     * test blobUrl has publicEndpoint
     *
     * @test
     */
    public function testBlobUrlHasPublicEndpoint(): void
    {
        $extension = new AzureStorageExtension(
            $this->createBlobRestProxyMock(),
            'https://public.example.com'
        );
        $container = 'test';
        $blob      = 'sample.txt';

        $result = $extension->blobUrl($container, $blob);

        $this->assertEquals('https://public.example.com/test/sample.txt', $result);
    }

    /**
     * test blobUrl do not has publicEndpoint
     *
     * @test
     */
    public function testBlobUrlDoNotHasPublicEndpoint(): void
    {
        $container = 'test';
        $blob      = 'sample.txt';
        $url       = 'https://storage.example.com/' . $container . '/' . $blob;

        $blobRestProxyMock = $this->createBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('getBlobUrl')
            ->once()
            ->with($container, $blob)
            ->andReturn($url);
        $extension = new AzureStorageExtension($blobRestProxyMock);

        $result = $extension->blobUrl($container, $blob);

        $this->assertEquals($url, $result);
    }
}
