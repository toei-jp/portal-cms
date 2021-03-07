<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Command\Cache\Clear;

use App\Console\Command\Cache\Clear\ViewCommand;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use RuntimeException;
use Slim\Views\Twig;
use Symfony\Component\Filesystem\Filesystem;
use Tests\Unit\Console\Command\AbstructTestCase;
use Twig\Cache\FilesystemCache;
use Twig\Cache\NullCache;

final class ViewCommandTest extends AbstructTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&ViewCommand
     */
    protected function createTargetMock()
    {
        return Mockery::mock(ViewCommand::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(ViewCommand::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Twig
     */
    protected function createTwigMock()
    {
        return Mockery::mock(Twig::class);
    }

    /**
     * @test
     */
    public function testConstruct(): void
    {
        $targetMock = $this->createTargetMock()
            ->makePartial();
        $targetRef  = $this->createTargetReflection();
        $twigMock   = $this->createTwigMock();

        $targetConstructor = $targetRef->getConstructor();
        $targetConstructor->invoke($targetMock, $twigMock);

        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $this->assertEquals($twigMock, $viewPropertyRef->getValue($targetMock));
    }

    /**
     * @test
     */
    public function testExecuteUseFilesystemCache(): void
    {
        $filesystemCacheMock = $this->createFilesystemCacheMock();

        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with(false)
            ->andReturn($filesystemCacheMock);

        $cacheDir = '/foo/bar/cache';
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with()
            ->andReturn($cacheDir);

        $twigMock = $this->createTwigMock();
        $twigMock
            ->shouldReceive('getEnvironment')
            ->with()
            ->andReturn($twigEnvironmentMock);

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $outputSpy = $this->createOutputSpy();
        $targetMock
            ->shouldReceive('clearFilesystemCache')
            ->once()
            ->with($cacheDir, $outputSpy);

        $inputMock = $this->createInputMock();

        $targetRef = $this->createTargetReflection();

        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $twigMock);

        $executeMethodRef = $targetRef->getMethod('execute');
        $executeMethodRef->setAccessible(true);

        // execute
        $result = $executeMethodRef->invoke($targetMock, $inputMock, $outputSpy);
        $this->assertEquals(0, $result);

        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::type('string'))
            ->once();
    }

    /**
     * @return MockInterface&LegacyMockInterface
     */
    protected function createTwigEnvironmentMock()
    {
        return Mockery::mock('TwigEnvironment');
    }

    /**
     * @return MockInterface&LegacyMockInterface&FilesystemCache
     */
    protected function createFilesystemCacheMock()
    {
        return Mockery::mock(FilesystemCache::class);
    }

    /**
     * @test
     */
    public function testExecuteUseNullCache(): void
    {
        $nullCache = $this->createNullCache();

        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with(false)
            ->andReturn($nullCache);

        $twigMock = $this->createTwigMock();
        $twigMock
            ->shouldReceive('getEnvironment')
            ->with()
            ->andReturn($twigEnvironmentMock);

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $targetMock
            ->shouldReceive('clearFilesystemCache')
            ->never();

        $inputMock = $this->createInputMock();
        $outputSpy = $this->createOutputSpy();

        $targetRef = $this->createTargetReflection();

        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $twigMock);

        $executeMethodRef = $targetRef->getMethod('execute');
        $executeMethodRef->setAccessible(true);

        // execute
        $result = $executeMethodRef->invoke($targetMock, $inputMock, $outputSpy);
        $this->assertEquals(0, $result);

        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::type('string'))
            ->twice();
    }

    /**
     * Create NullCache
     *
     * finalが指定されたクラスはプロキシパーシャルテストダブルを使うことになるが、
     * instanceofのチェックをパスできないのでモックしない。
     */
    protected function createNullCache(): NullCache
    {
        return new NullCache();
    }

    /**
     * @test
     */
    public function testExecuteUseOtherCache(): void
    {
        $otherCache = $this->createOtherCacheMock();

        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->shouldReceive('getCache')
            ->once()
            ->with(false)
            ->andReturn($otherCache);

        $twigMock = $this->createTwigMock();
        $twigMock
            ->shouldReceive('getEnvironment')
            ->with()
            ->andReturn($twigEnvironmentMock);

        $targetMock = $this->createTargetMock()
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $targetMock
            ->shouldReceive('clearFilesystemCache')
            ->never();

        $inputMock = $this->createInputMock();
        $outputSpy = $this->createOutputSpy();

        $targetRef = $this->createTargetReflection();

        $viewPropertyRef = $targetRef->getProperty('view');
        $viewPropertyRef->setAccessible(true);
        $viewPropertyRef->setValue($targetMock, $twigMock);

        $this->expectException(RuntimeException::class);

        $executeMethodRef = $targetRef->getMethod('execute');
        $executeMethodRef->setAccessible(true);

        // execute
        $executeMethodRef->invoke($targetMock, $inputMock, $outputSpy);
    }

    /**
     * @return MockInterface&LegacyMockInterface
     */
    protected function createOtherCacheMock()
    {
        return Mockery::mock('OtherCache');
    }

    /**
     * @preserveGlobalState disabled
     * @runInSeparateProcess
     * @test
     */
    public function testClearFilesystemCache(): void
    {
        $dir = '/foo/bar/cache';

        $filesystemMock = $this->createFilesystemMock();
        $filesystemMock
            ->shouldReceive('remove')
            ->once()
            ->with($dir);

        $outputSpy = $this->createOutputSpy();

        $targetMock = $this->createTargetMock();
        $targetRef  = $this->createTargetReflection();

        $clearFilesystemCacheMethodRef = $targetRef->getMethod('clearFilesystemCache');
        $clearFilesystemCacheMethodRef->setAccessible(true);

        // execute
        $clearFilesystemCacheMethodRef->invoke($targetMock, $dir, $outputSpy);

        $outputSpy
            ->shouldHaveReceived('writeln')
            ->with(Mockery::type('string'))
            ->twice();
    }

    /**
     * @return MockInterface&LegacyMockInterface
     */
    protected function createFilesystemMock()
    {
        return Mockery::mock('overload:' . Filesystem::class);
    }
}
