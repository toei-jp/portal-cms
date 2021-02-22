<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\AuthController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class AuthControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&AuthController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AuthController::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(AuthController::class);
    }

    /**
     * @test
     */
    public function testRnederLogin(): void
    {
        $responseMock = $this->createResponseMock();
        $data         = ['foo' => 'bar'];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'auth/login.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderLoginMethodRef = $targetRef->getMethod('renderLogin');
        $renderLoginMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderLoginMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }

    /**
     * @test
     */
    public function testExecuteLogin(): void
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();
        $args         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('renderLogin')
            ->once()
            ->with($responseMock)
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeLogin($requestMock, $responseMock, $args)
        );
    }
}
