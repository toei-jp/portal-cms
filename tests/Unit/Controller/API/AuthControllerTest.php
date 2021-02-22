<?php

declare(strict_types=1);

namespace Tests\Unit\Controller\API;

use App\Controller\API\AuthController;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\Unit\Controller\BaseTestCase;

final class AuthControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&AuthController
     */
    protected function createTargetMock()
    {
        return Mockery::mock(AuthController::class);
    }

    /**
     * @test
     */
    public function testExecuteToken(): void
    {
        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();
        $args         = [];

        $targetMock = $this->createTargetMock();
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $tokenData = ['token' => 'abcdefg'];

        $requestTokenResponseMock = $this->createRequestTokenResponseMock();
        $requestTokenResponseMock
            ->shouldReceive('getBody->getContents')
            ->andReturn(json_encode($tokenData));

        $targetMock
            ->shouldReceive('requestToken')
            ->once()
            ->with()
            ->andReturn($requestTokenResponseMock);

        $meta = ['name' => 'Authorization Token'];
        $responseMock
            ->shouldReceive('withJson')
            ->once()
            ->with([
                'meta' => $meta,
                'data' => $tokenData,
            ])
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeToken($requestMock, $responseMock, $args)
        );
    }

    /**
     * @return MockInterface&LegacyMockInterface&ResponseInterface
     */
    protected function createRequestTokenResponseMock()
    {
        return Mockery::mock(ResponseInterface::class);
    }
}
