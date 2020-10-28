<?php

/**
 * NewsController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Controller;

use Toei\PortalAdmin\Controller\Traits\ImageResize;
use Toei\PortalAdmin\Form;
use Toei\PortalAdmin\ORM\Entity;
use Slim\Exception\NotFoundException;

/**
 * News controller
 */
class NewsController extends BaseController
{
    use ImageResize;

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

        $form = new Form\NewsFindForm($this->em);
        $form->setData($request->getParams());
        $cleanValues = [];

        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values      = $cleanValues;
        } else {
            $values = $request->getParams();
            $this->data->set('errors', $form->getMessages());
        }

        $user = $this->auth->getUser();

        if ($user->isTheater()) {
            // ひとまず検索のパラメータとして扱う
            $cleanValues['user'] = $user->getId();
        }

        $this->data->set('form', $form);
        $this->data->set('values', $values);
        $this->data->set('params', $cleanValues);

        /** @var \Toei\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\News::class)->findForList($cleanValues, $page);

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
        $form = new Form\NewsForm(Form\NewsForm::TYPE_NEW);
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
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\NewsForm(Form\NewsForm::TYPE_NEW);
        $form->setData($params);

        if (! $form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'new';
        }

        $cleanData = $form->getData();

        $image = $cleanData['image'];

        // rename
        $newName = Entity\File::createName($image['name']);

        // SASAKI-245
        $imageStream = $this->resizeImage($image['tmp_name'], 1200, 600);

        // upload storage
        // @todo storageと同期するような仕組みをFileへ
        $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
        $options->setContentType($image['type']);
        $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            $imageStream,
            $options
        );

        $file = new Entity\File();
        $file->setName($newName);
        $file->setOriginalName($image['name']);
        $file->setMimeType($image['type']);
        $file->setSize($imageStream->getSize());

        $this->em->persist($file);

        // title
        $title = null;

        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }

        $news = new Entity\News();

        $news->setTitle($title);
        $news->setImage($file);
        $news->setCategory((int) $cleanData['category']);
        $news->setStartDt($cleanData['start_dt']);
        $news->setEndDt($cleanData['end_dt']);
        $news->setHeadline($cleanData['headline']);
        $news->setBody($cleanData['body']);
        $news->setCreatedUser($this->auth->getUser());
        $news->setUpdatedUser($this->auth->getUser());

        $this->em->persist($news);
        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('NEWS・インフォメーション「%s」を追加しました。', $news->getHeadline()),
        ]);

        $this->redirect(
            $this->router->pathFor('news_edit', ['id' => $news->getId()]),
            303
        );
    }

    /**
     * edit action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeEdit($request, $response, $args)
    {
        $news = $this->em->getRepository(Entity\News::class)->findOneById($args['id']);

        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\News $news */

        $this->data->set('news', $news);

        $values = [
            'id'         => $news->getId(),
            'title_id'   => null,
            'title_name' => null,
            'category'   => $news->getCategory(),
            'start_dt'   => $news->getStartDt()->format('Y/m/d H:i'),
            'end_dt'     => $news->getEndDt()->format('Y/m/d H:i'),
            'headline'   => $news->getHeadline(),
            'body'       => $news->getBody(),
        ];

        if ($news->getTitle()) {
            $values['title_id']   = $news->getTitle()->getId();
            $values['title_name'] = $news->getTitle()->getName();
        }

        $this->data->set('values', $values);

        $form = new Form\NewsForm(Form\NewsForm::TYPE_EDIT);
        $this->data->set('form', $form);
    }

    /**
     * update action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeUpdate($request, $response, $args)
    {
        $news = $this->em->getRepository(Entity\News::class)->findOneById($args['id']);

        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\News $news */

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\NewsForm(Form\NewsForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            $this->data->set('news', $news);
            $this->data->set('form', $form);
            $this->data->set('values', $request->getParams());
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'edit';
        }

        $cleanData = $form->getData();

        $image = $cleanData['image'];

        if ($image['name']) {
            // rename
            $newName = Entity\File::createName($image['name']);

            // SASAKI-245
            $imageStream = $this->resizeImage($image['tmp_name'], 1200, 600);

            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
            $options->setContentType($image['type']);
            $this->bc->createBlockBlob(
                Entity\File::getBlobContainer(),
                $newName,
                $imageStream,
                $options
            );

            $file = new Entity\File();
            $file->setName($newName);
            $file->setOriginalName($image['name']);
            $file->setMimeType($image['type']);
            $file->setSize($imageStream->getSize());

            $this->em->persist($file);

            $oldImage = $news->getImage();
            $news->setImage($file);

            // @todo preUpdateで出来ないか？ hasChangedField()
            $this->em->remove($oldImage);

            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());
        }

        $title = null;

        if ($cleanData['title_id']) {
            $title =  $this->em->getRepository(Entity\Title::class)->findOneById($cleanData['title_id']);
        }

        $news->setTitle($title);

        $news->setCategory((int) $cleanData['category']);
        $news->setStartDt($cleanData['start_dt']);
        $news->setEndDt($cleanData['end_dt']);
        $news->setHeadline($cleanData['headline']);
        $news->setBody($cleanData['body']);
        $news->setUpdatedUser($this->auth->getUser());

        $this->em->persist($news);
        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('NEWS・インフォメーション「%s」を編集しました。', $news->getHeadline()),
        ]);

        $this->redirect(
            $this->router->pathFor('news_edit', ['id' => $news->getId()]),
            303
        );
    }

    /**
     * delete action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeDelete($request, $response, $args)
    {
        $news = $this->em->getRepository(Entity\News::class)->findOneById($args['id']);

        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }

        /**@var Entity\News $news */

        $this->doDelete($news);

        $this->logger->info('Delete "News".', ['id' => $news->getId()]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('NEWS・インフォメーション「%s」を削除しました。', $news->getHeadline()),
        ]);

        $this->redirect($this->router->pathFor('news_list'), 303);
    }

    /**
     * do delete
     *
     * @param Entity\News $news
     * @return void
     */
    protected function doDelete(Entity\News $news)
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $news->setIsDeleted(true);
            $news->setUpdatedUser($this->auth->getUser());

            $this->logger->debug('Soft delete "News".', [
                'id' => $news->getId(),
            ]);

            $this->em->flush();

            $pageNewsDeleteCount = $this->em
                ->getRepository(Entity\PageNews::class)
                ->deleteByNews($news);

            $this->logger->debug('Delete "PageNews"', ['count' => $pageNewsDeleteCount]);

            $theaterNewsDeleteCount = $this->em
                ->getRepository(Entity\TheaterNews::class)
                ->deleteByNews($news);

            $this->logger->debug('Delete "TheaterNews"', ['count' => $theaterNewsDeleteCount]);

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * publication action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executePublication($request, $response, $args)
    {
        $user = $this->auth->getUser();

        /** @var Entity\Page[] $pages */
        $pages = [];

        if (! $user->isTheater()) {
            $pages = $this->em->getRepository(Entity\Page::class)->findActive();
        }

        $this->data->set('pages', $pages);

        $theaterRepository = $this->em->getRepository(Entity\Theater::class);

        if ($user->isTheater()) {
            /** @var Entity\Theater[] $theaters */
            $theaters = [$theaterRepository->findOneById($user->getTheater()->getId())];
        } else {
            /** @var Entity\Theater[] $theaters */
            $theaters = $theaterRepository->findActive();
        }

        $this->data->set('theaters', $theaters);
    }

    /**
     * publication update action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executePublicationUpdate($request, $response, $args)
    {
        $target = $args['target'];

        $form = new Form\NewsPublicationForm($target, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            throw new \LogicException('invalid parameters.');
        }

        $cleanData       = $form->getData();
        $targetEntity    = null;
        $basePublication = null;

        if ($target === Form\NewsPublicationForm::TARGET_TEATER) {
            /** @var Entity\Theater $targetEntity */
            $targetEntity    = $this->em
                ->getRepository(Entity\Theater::class)
                ->findOneById((int) $cleanData['theater_id']);
            $basePublication = new Entity\TheaterNews();
            $basePublication->setTheater($targetEntity);
        } elseif ($target === Form\NewsPublicationForm::TARGET_PAGE) {
            /** @var Entity\Page $targetEntity */
            $targetEntity    = $this->em
                ->getRepository(Entity\Page::class)
                ->findOneById((int) $cleanData['page_id']);
            $basePublication = new Entity\PageNews();
            $basePublication->setPage($targetEntity);
        }

        // いったん削除する
        $targetEntity->getNewsList()->clear();

        foreach ($cleanData['news_list'] as $newsData) {
            $publication = clone $basePublication;

            $news = $this->em
                ->getRepository(Entity\News::class)
                ->findOneById((int) $newsData['news_id']);

            if (! $news) {
                // @todo formで検証したい
                throw new \LogicException('invalid news.');
            }

            /** @var Entity\News $news */

            $publication->setNews($news);
            $publication->setDisplayOrder((int) $newsData['display_order']);

            $this->em->persist($publication);
        }

        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('%sの表示順を保存しました。', $targetEntity->getNameJa()),
        ]);

        $this->redirect($this->router->pathFor('news_publication'), 303);
    }
}
