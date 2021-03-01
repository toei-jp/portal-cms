<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use App\Controller\AdminUserController;
use App\ORM\Entity\AdminUser;
use App\ORM\Repository\AdminUserRepository;
use App\Pagination\DoctrinePaginator;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use ReflectionClass;
use Slim\Container;

final class AdminUserControllerTest extends BaseTestCase
{
    /**
     * @return MockInterface&LegacyMockInterface&AdminUserController
     */
    protected function createTargetMock(Container $container)
    {
        return Mockery::mock(AdminUserController::class, [$container]);
    }

    protected function createTargetReflection(): ReflectionClass
    {
        return new ReflectionClass(AdminUserController::class);
    }

    /**
     * @return MockInterface&LegacyMockInterface&AdminUserRepository
     */
    protected function createAdminUserRepositoryMock()
    {
        return Mockery::mock(AdminUserRepository::class);
    }

    /**
     * @test
     */
    public function testExecuteList(): void
    {
        $page   = 2;
        $params = [];

        $requestMock  = $this->createRequestMock();
        $responseMock = $this->createResponseMock();
        $args         = [];

        $container = $this->createContainer();

        $repositoryMock = $this->createAdminUserRepositoryMock();

        $pagenaterMock = $this->createPaginatorMock();
        $repositoryMock
            ->shouldReceive('findForList')
            ->once()
            ->with($params, $page)
            ->andReturn($pagenaterMock);

        $container['em']
            ->shouldReceive('getRepository')
            ->once()
            ->with(AdminUser::class)
            ->andReturn($repositoryMock);

        $requestMock
            ->shouldReceive('getParam')
            ->once()
            ->with('p', 1)
            ->andReturn($page);

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $data = [
            'page' => $page,
            'params' => $params,
            'pagenater' => $pagenaterMock,
        ];
        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'admin_user/list.html.twig', $data)
            ->andReturn($responseMock);

        $this->assertEquals(
            $responseMock,
            $targetMock->executeList($requestMock, $responseMock, $args)
        );
    }

    /**
     * @return MockInterface&LegacyMockInterface&DoctrinePaginator
     */
    protected function createPaginatorMock()
    {
        return Mockery::mock(DoctrinePaginator::class);
    }

    /**
     * @test
     */
    public function testRenderNew(): void
    {
        $responseMock = $this->createResponseMock();
        $data         = ['foo' => 'bar'];

        $container = $this->createContainer();

        $targetMock = $this->createTargetMock($container);
        $targetMock
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetMock
            ->shouldReceive('render')
            ->once()
            ->with($responseMock, 'admin_user/new.html.twig', $data)
            ->andReturn($responseMock);

        $targetRef = $this->createTargetReflection();

        $renderNewMethodRef = $targetRef->getMethod('renderNew');
        $renderNewMethodRef->setAccessible(true);

        $this->assertEquals(
            $responseMock,
            $renderNewMethodRef->invoke($targetMock, $responseMock, $data)
        );
    }
}
