<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\ImageResize;
use App\Form;
use App\ORM\Entity;
use App\Pagination\DoctrinePaginator;
use LogicException;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Throwable;

class NewsController extends BaseController
{
    use ImageResize;

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $page = (int) $request->getParam('p', 1);

        $form = new Form\NewsFindForm($this->em);
        $form->setData($request->getParams());
        $cleanValues = [];
        $errors      = [];

        if ($form->isValid()) {
            $cleanValues = $form->getData();
            $values      = $cleanValues;
        } else {
            $values = $request->getParams();
            $errors = $form->getMessages();
        }

        $user = $this->auth->getUser();

        if ($user->isTheater()) {
            // ひとまず検索のパラメータとして扱う
            $cleanValues['user'] = $user->getId();
        }

        /** @var DoctrinePaginator $pagenater */
        $pagenater = $this->em
            ->getRepository(Entity\News::class)
            ->findForList($cleanValues, $page);

        return $this->render($response, 'news/list.html.twig', [
            'form' => $form,
            'values' => $values,
            'params' => $cleanValues,
            'errors' => $errors,
            'pagenater' => $pagenater,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderNew(Response $response, array $data = []): Response
    {
        return $this->render($response, 'news/new.html.twig', $data);
    }

    /**
     * new action
     *
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        $form = new Form\NewsForm(Form\NewsForm::TYPE_NEW);

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * create action
     *
     * @param array<string, mixed> $args
     */
    public function executeCreate(Request $request, Response $response, array $args): Response
    {
        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\NewsForm(Form\NewsForm::TYPE_NEW);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $image = $cleanData['image'];

        // rename
        $newName = Entity\File::createName($image['name']);

        // SASAKI-245
        $imageStream = $this->resizeImage($image['tmp_name'], 1200, 600);

        // upload storage
        // @todo storageと同期するような仕組みをFileへ
        $options = new CreateBlockBlobOptions();
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
            $title = $this->em
                ->getRepository(Entity\Title::class)
                ->findOneById((int) $cleanData['title_id']);
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
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data = []): Response
    {
        return $this->render($response, 'news/edit.html.twig', $data);
    }

    /**
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\News|null $news */
        $news = $this->em
            ->getRepository(Entity\News::class)
            ->findOneById((int) $args['id']);

        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form\NewsForm(Form\NewsForm::TYPE_EDIT);

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

        return $this->renderEdit($response, [
            'news' => $news,
            'form' => $form,
            'values' => $values,
        ]);
    }

    /**
     * update action
     *
     * @param array<string, mixed> $args
     */
    public function executeUpdate(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\News|null $news */
        $news = $this->em
            ->getRepository(Entity\News::class)
            ->findOneById((int) $args['id']);

        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\NewsForm(Form\NewsForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'news' => $news,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
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
            $options = new CreateBlockBlobOptions();
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
            $title = $this->em
                ->getRepository(Entity\Title::class)
                ->findOneById((int) $cleanData['title_id']);
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
     * @param array<string, mixed> $args
     */
    public function executeDelete(Request $request, Response $response, array $args): void
    {
        /** @var Entity\News|null $news */
        $news = $this->em
            ->getRepository(Entity\News::class)
            ->findOneById((int) $args['id']);

        if (is_null($news)) {
            throw new NotFoundException($request, $response);
        }

        $this->doDelete($news);

        $this->logger->info('Delete "News".', ['id' => $news->getId()]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('NEWS・インフォメーション「%s」を削除しました。', $news->getHeadline()),
        ]);

        $this->redirect($this->router->pathFor('news_list'), 303);
    }

    protected function doDelete(Entity\News $news): void
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
        } catch (Throwable $e) {
            $this->em->getConnection()->rollBack();

            throw $e;
        }
    }

    /**
     * publication action
     *
     * @param array<string, mixed> $args
     */
    public function executePublication(Request $request, Response $response, array $args): Response
    {
        $user = $this->auth->getUser();

        /** @var Entity\Page[] $pages */
        $pages = [];

        if (! $user->isTheater()) {
            $pages = $this->em->getRepository(Entity\Page::class)->findActive();
        }

        $theaterRepository = $this->em->getRepository(Entity\Theater::class);

        if ($user->isTheater()) {
            /** @var Entity\Theater[] $theaters */
            $theaters = [$theaterRepository->findOneById($user->getTheater()->getId())];
        } else {
            /** @var Entity\Theater[] $theaters */
            $theaters = $theaterRepository->findActive();
        }

        return $this->render($response, 'news/publication.html.twig', [
            'pages' => $pages,
            'theaters' => $theaters,
        ]);
    }

    /**
     * publication update action
     *
     * @param array<string, mixed> $args
     */
    public function executePublicationUpdate(Request $request, Response $response, array $args): void
    {
        $target = $args['target'];

        $form = new Form\NewsPublicationForm($target, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            throw new LogicException('invalid parameters.');
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

            /** @var Entity\News|null $news */
            $news = $this->em
                ->getRepository(Entity\News::class)
                ->findOneById((int) $newsData['news_id']);

            if (! $news) {
                // @todo formで検証したい
                throw new LogicException('invalid news.');
            }

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
