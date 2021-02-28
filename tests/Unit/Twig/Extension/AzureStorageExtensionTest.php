<?php

declare(strict_types=1);

namespace Tests\Unit\Twig\Extension;

use App\Twig\Extension\AzureStorageExtension;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class AzureStorageExtensionTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&AzureStorageExtension
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AzureStorageExtension::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(AzureStorageExtension::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&BlobRestProxy
     */
    protected function crateBlobRestProxyMock()
    {
        return Mockery::mock(BlobRestProxy::class);
    }

    /**
     * @test
     */
    public function testConstruct(): void
    {
        $targetMock        = $this->createTargetMock();
        $blobRestProxyMock = $this->crateBlobRestProxyMock();
        $publicEndpoint    = 'http://example.com';

        // execute constructor
        $targetRef      = $this->createTargetReflection();
        $constructorRef = $targetRef->getConstructor();
        $constructorRef->invoke($targetMock, $blobRestProxyMock, $publicEndpoint);

        // test property "client"
        $clientPropertyRef = $targetRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $this->assertEquals(
            $blobRestProxyMock,
            $clientPropertyRef->getValue($targetMock)
        );

        // test property "publicEndpoint"
        $publicEndpointPropertyRef = $targetRef->getProperty('publicEndpoint');
        $publicEndpointPropertyRef->setAccessible(true);
        $this->assertEquals(
            $publicEndpoint,
            $publicEndpointPropertyRef->getValue($targetMock)
        );
    }

    /**
     * test blobUrl has publicEndpoint
     *
     * @test
     */
    public function testBlobUrlHasPublicEndpoint(): void
    {
        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $targetRef = $this->createTargetReflection();

        $publicEndpointPropertyRef = $targetRef->getProperty('publicEndpoint');
        $publicEndpointPropertyRef->setAccessible(true);

        $publicEndpoint = 'http://example.com';
        $publicEndpointPropertyRef->setValue($targetMock, $publicEndpoint);

        $container = 'test';
        $blob      = 'sample.txt';

        // execute
        $result = $targetMock->blobUrl($container, $blob);
        $this->assertStringContainsString($publicEndpoint, $result);
        $this->assertStringContainsString($container, $result);
        $this->assertStringContainsString($blob, $result);
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
        $url       = 'http://storage.example.com/' . $container . '/' . $blob;

        $targetMock = $this->createTargetMock();
        $targetMock->makePartial();

        $blobRestProxyMock = $this->crateBlobRestProxyMock();
        $blobRestProxyMock
            ->shouldReceive('getBlobUrl')
            ->once()
            ->with($container, $blob)
            ->andReturn($url);

        $targetRef = $this->createTargetReflection();

        $clientPropertyRef = $targetRef->getProperty('client');
        $clientPropertyRef->setAccessible(true);
        $clientPropertyRef->setValue($targetMock, $blobRestProxyMock);

        $publicEndpointPropertyRef = $targetRef->getProperty('publicEndpoint');
        $publicEndpointPropertyRef->setAccessible(true);
        $publicEndpointPropertyRef->setValue($targetMock, null);

        // execute
        $result = $targetMock->blobUrl($container, $blob);
        $this->assertEquals($url, $result);
    }
}
