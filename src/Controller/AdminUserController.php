<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use App\Pagination\DoctrinePaginator;
use Slim\Http\Request;
use Slim\Http\Response;

class AdminUserController extends BaseController
{
    protected function preExecute(Request $request, Response $response): void
    {
        $user = $this->auth->getUser();

        if (! $user->isMaster()) {
            throw new ForbiddenException();
        }

        parent::preExecute($request, $response);
    }

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $page = (int) $request->getParam('p', 1);

        $cleanValues = [];

        /** @var DoctrinePaginator $pagenater */
        $pagenater = $this->em
            ->getRepository(Entity\AdminUser::class)
            ->findForList($cleanValues, $page);

        return $this->render($response, 'admin_user/list.html.twig', [
            'page' => $page,
            'params' => $cleanValues,
            'pagenater' => $pagenater,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderNew(Response $response, array $data): Response
    {
        return $this->render($response, 'admin_user/new.html.twig', $data);
    }

    /**
     * new action
     *
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        $form = new Form\AdminUserForm($this->em);

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * create action
     *
     * @param array<string, mixed> $args
     */
    public function executeCreate(Request $request, Response $response, array $args): Response
    {
        $form = new Form\AdminUserForm($this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $adminUser = new Entity\AdminUser();
        $this->em->persist($adminUser);

        $adminUser->setName($cleanData['name']);
        $adminUser->setDisplayName($cleanData['display_name']);
        $adminUser->setPassword($cleanData['password']);
        $adminUser->setGroup((int) $cleanData['group']);

        if ($adminUser->isTheater()) {
            $theater = $this->em
                ->getRepository(Entity\Theater::class)
                ->findOneById((int) $cleanData['theater']);

            $adminUser->setTheater($theater);
        }

        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('管理ユーザ「%s」を追加しました。', $adminUser->getDisplayName()),
        ]);

        // @todo 編集ページへリダイレクト
        $this->redirect(
            $this->router->pathFor('admin_user_list'),
            303
        );
    }
}
