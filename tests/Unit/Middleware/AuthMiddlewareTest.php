<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Toei\PortalAdmin\Middleware\AuthMiddleware;

/**
 * Auth Middleware test
 */
final class AuthMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Create target mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|AuthMiddleware
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AuthMiddleware::class);
    }

    /**
     * Create target reflection
     *
     * @return \ReflectionClass
     */
    protected function createTargetReflection()
    {
        return new \ReflectionClass(AuthMiddleware::class);
    }

    /**
     * Create Container mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|ContainerInterface
     */
    protected function createContainerMock()
    {
        return Mockery::mock(ContainerInterface::class);
    }

    /**
     * Create Auth mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected function createAuthMock()
    {
        return Mockery::mock('Auth');
    }

    /**
     * Create Router mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected function createRouterMock()
    {
        return Mockery::mock('Router');
    }

    /**
     * Create Request mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|ServerRequestInterface
     */
    protected function createRequestMock()
    {
        return Mockery::mock(ServerRequestInterface::class);
    }

    /**
     * Create Response mock
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface|Response
     */
    protected function createResponseMock()
    {
        return Mockery::mock(Response::class);
    }

    /**
     * test __invoke (is not authenticated)
     *
     * @test
     *
     * @return void
     */
    public function testInvokeIsNotAuthenticated()
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
     *
     * @return void
     */
    public function testInvokeIsAuthenticated()
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
