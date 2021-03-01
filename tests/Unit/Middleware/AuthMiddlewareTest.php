<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Middleware\AuthMiddleware;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use Slim\Http\Response;

final class AuthMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return MockInterface&LegacyMockInterface&AuthMiddleware
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AuthMiddleware::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(AuthMiddleware::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&ContainerInterface
     */
    protected function createContainerMock()
    {
        return Mockery::mock(ContainerInterface::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface
     */
    protected function createAuthMock()
    {
        return Mockery::mock('Auth');
    }

    /**
     * @return MockInterface&LegacyMockInterface
     */
    protected function createRouterMock()
    {
        return Mockery::mock('Router');
    }

    /**
     * @return MockInterface&LegacyMockInterface&ServerRequestInterface
     */
    protected function createRequestMock()
    {
        return Mockery::mock(ServerRequestInterface::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&Response
     */
    protected function createResponseMock()
    {
        return Mockery::mock(Response::class);
    }

    /**
     * test __invoke (is not authenticated)
     *
     * @test
     */
    public function testInvokeIsNotAuthenticated(): void
    {
        $authMock = $this->createAuthMock();
        $authMock
            ->shouldReceive('isAuthenticated')
            ->with()
            ->andReturn(false);

        $containerMock = $this->createContainerMock();
        $containerMock
            ->shouldReceive('get')
            ->once()
            ->with('auth')
            ->andReturn($authMock);

        $redirectUrl = 'https://example.com/redirect';
        $routerMock  = $this->createRouterMock();
        $routerMock
            ->shouldReceive('pathFor')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn($redirectUrl);

        $containerMock
            ->shouldReceive('get')
            ->once()
            ->with('router')
            ->andReturn($routerMock);

        $targetMock = $this->createTargetMock()
            ->makePartial();

        $targetRef = $this->createTargetReflection();

        $containerPropertyRef = $targetRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($targetMock, $containerMock);

        $responseMock = $this->createResponseMock();
        $responseMock
            ->shouldReceive('withRedirect')
            ->once()
            ->with($redirectUrl)
            ->andReturn($responseMock);

        $requestMock = $this->createRequestMock();

        $next = static function ($request, $response) {
            return $response;
        };

        $result = $targetMock($requestMock, $responseMock, $next);
        $this->assertEquals($responseMock, $result);
    }

    /**
     * test __invoke (is authenticated)
     *
     * @test
     */
    public function testInvokeIsAuthenticated(): void
    {
        $authMock = $this->createAuthMock();
        $authMock
            ->shouldReceive('isAuthenticated')
            ->with()
            ->andReturn(true);

        $containerMock = $this->createContainerMock();
        $containerMock
            ->shouldReceive('get')
            ->once()
            ->with('auth')
            ->andReturn($authMock);

        $targetMock = $this->createTargetMock()
            ->makePartial();

        $targetRef = $this->createTargetReflection();

        $containerPropertyRef = $targetRef->getProperty('container');
        $containerPropertyRef->setAccessible(true);
        $containerPropertyRef->setValue($targetMock, $containerMock);

        $responseMock = $this->createResponseMock();
        $responseMock
            ->shouldReceive('withRedirect')
            ->never();

        $requestMock = $this->createRequestMock();

        $next = static function ($request, $response) {
            return $response;
        };

        $result = $targetMock($requestMock, $responseMock, $next);
        $this->assertEquals($responseMock, $result);
    }
}
