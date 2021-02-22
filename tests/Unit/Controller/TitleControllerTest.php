<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\TitleController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class TitleControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&TitleController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(TitleController::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(TitleController::class);
    }

    /**
     * @test
     */
    public function testExecuteImport(): void
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();
        $args         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'title/import.html.twig')
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeImport($requestMock, $responseMock, $args)
        );
    }

    /**
     * @test
     */
    public function testRenderNew(): void
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
            ->with($responseMock, 'title/new.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderNewMethodRef = $targetRef->getMethod('renderNew');
        $renderNewMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderNewMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }

    /**
     * @test
     */
    public function testRenderEdit(): void
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
            ->with($responseMock, 'title/edit.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderEditMethodRef = $targetRef->getMethod('renderEdit');
        $renderEditMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderEditMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }
}
