<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\TheaterMetaController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class TheaterMetaControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&TheaterMetaController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(TheaterMetaController::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(TheaterMetaController::class);
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
            ->with($responseMock, 'theater_meta/opening_hour/edit.html.twig', $data)
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
