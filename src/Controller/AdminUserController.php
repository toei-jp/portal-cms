<?php

namespace Toei\PortalAdmin\Controller;

use Toei\PortalAdmin\Form\LoginForm;
use Toei\PortalAdmin\Exception\ForbiddenException;
use Toei\PortalAdmin\Form;
use Toei\PortalAdmin\ORM\Entity;

/**
 * AdminUser controller class
 */
class AdminUserController extends BaseController
{
    protected function preExecute($request, $response): void
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
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeList($request, $response, $args)
    {
        $page = (int) $request->getParam('p', 1);
        $this->data->set('page', $page);

        $cleanValues = [];
        $this->data->set('params', $cleanValues);

        /** @var \Toei\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\AdminUser::class)->findForList($cleanValues, $page);

        $this->data->set('pagenater', $pagenater);
    }

    /**
     * new action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeNew($request, $response, $args)
    {
        $form = new Form\AdminUserForm($this->em);
        $this->data->set('form', $form);
    }

    /**
     * create action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeCreate($request, $response, $args)
    {
        $form = new Form\AdminUserForm($this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'new';
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
                ->findOneById($cleanData['theater']);

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
