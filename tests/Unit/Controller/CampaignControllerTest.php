<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\CampaignController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;

final class CampaignControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&CampaignController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(CampaignController::class);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(CampaignController::class);
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
            ->with($responseMock, 'campaign/new.html.twig', $data)
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
    public function testExecuteNew(): void
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();
        $args         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('renderNew')
            ->once()
            ->with($responseMock)
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeNew($requestMock, $responseMock, $args)
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
            ->with($responseMock, 'campaign/edit.html.twig', $data)
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
