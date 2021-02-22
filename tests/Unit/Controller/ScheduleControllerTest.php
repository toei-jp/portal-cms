<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\ScheduleController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class ScheduleControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&ScheduleController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(ScheduleController::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(ScheduleController::class);
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
            ->with($responseMock, 'schedule/new.html.twig', $data)
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
            ->with($responseMock, 'schedule/edit.html.twig', $data)
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
