<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use App\Pagination\DoctrinePaginator;
use LogicException;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Throwable;

class MainBannerController extends BaseController
{
    protected function preExecute(Request $request, Response $response): void
    {
        $user = $this->auth->getUser();

        if ($user->isTheater()) {
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

        $form = new Form\MainBannerFindForm();
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

        /** @var DoctrinePaginator $pagenater */
        $pagenater = $this->em
            ->getRepository(Entity\MainBanner::class)
            ->findForList($cleanValues, $page);

        return $this->render($response, 'main_banner/list.html.twig', [
            'page' => $page,
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
        return $this->render($response, 'main_banner/new.html.twig', $data);
    }

    /**
     * new action
     *
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_NEW);

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

        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_NEW);
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

        // upload storage
        // @todo storageと同期するような仕組みをFileへ
        $options = new CreateBlockBlobOptions();
        $options->setContentType($image['type']);
        $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            fopen($image['tmp_name'], 'r'),
            $options
        );

        $file = new Entity\File();
        $file->setName($newName);
        $file->setOriginalName($image['name']);
        $file->setMimeType($image['type']);
        $file->setSize((int) $image['size']);

        $this->em->persist($file);

        $mainBanner = new Entity\MainBanner();
        $mainBanner->setImage($file);
        $mainBanner->setName($cleanData['name']);
        $mainBanner->setLinkType((int) $cleanData['link_type']);
        $mainBanner->setLinkUrl($cleanData['link_url']);
        $mainBanner->setCreatedUser($this->auth->getUser());
        $mainBanner->setUpdatedUser($this->auth->getUser());

        $this->em->persist($mainBanner);
        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を追加しました。', $mainBanner->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('main_banner_edit', ['id' => $mainBanner->getId()]),
            303
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data = []): Response
    {
        return $this->render($response, 'main_banner/edit.html.twig', $data);
    }

    /**
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\MainBanner|null $mainBanner */
        $mainBanner = $this->em
            ->getRepository(Entity\MainBanner::class)
            ->findOneById((int) $args['id']);

        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_EDIT);

        $values = [
            'id'        => $mainBanner->getId(),
            'name'      => $mainBanner->getName(),
            'link_type' => $mainBanner->getLinkType(),
            'link_url'  => $mainBanner->getLinkUrl(),
        ];

        return $this->renderEdit($response, [
            'mainBanner' => $mainBanner,
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
        /** @var Entity\MainBanner|null $mainBanner */
        $mainBanner = $this->em
            ->getRepository(Entity\MainBanner::class)
            ->findOneById((int) $args['id']);

        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\MainBannerForm(Form\MainBannerForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'mainBanner' => $mainBanner,
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

            // upload storage
            // @todo storageと同期するような仕組みをFileへ
            $options = new CreateBlockBlobOptions();
            $options->setContentType($image['type']);
            $this->bc->createBlockBlob(
                Entity\File::getBlobContainer(),
                $newName,
                fopen($image['tmp_name'], 'r'),
                $options
            );

            $file = new Entity\File();
            $file->setName($newName);
            $file->setOriginalName($image['name']);
            $file->setMimeType($image['type']);
            $file->setSize((int) $image['size']);

            $this->em->persist($file);

            $oldImage = $mainBanner->getImage();
            $mainBanner->setImage($file);

            // @todo preUpdateで出来ないか？ hasChangedField()
            $this->em->remove($oldImage);

            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());
        }

        $mainBanner->setName($cleanData['name']);
        $mainBanner->setLinkType((int) $cleanData['link_type']);
        $mainBanner->setLinkUrl($cleanData['link_url']);
        $mainBanner->setUpdatedUser($this->auth->getUser());

        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を編集しました。', $mainBanner->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('main_banner_edit', ['id' => $mainBanner->getId()]),
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
        /** @var Entity\MainBanner|null $mainBanner */
        $mainBanner = $this->em
            ->getRepository(Entity\MainBanner::class)
            ->findOneById((int) $args['id']);

        if (is_null($mainBanner)) {
            throw new NotFoundException($request, $response);
        }

        $this->doDelete($mainBanner);

        $this->logger->info('Delete "MainBanner".', ['id' => $mainBanner->getId()]);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('メインバナー「%s」を削除しました。', $mainBanner->getName()),
        ]);

        $this->redirect($this->router->pathFor('main_banner_list'), 303);
    }

    protected function doDelete(Entity\MainBanner $mainBanner): void
    {
        $this->em->getConnection()->beginTransaction();

        try {
            $mainBanner->setIsDeleted(true);
            $mainBanner->setUpdatedUser($this->auth->getUser());

            $this->logger->debug('Soft delete "MainBanner".', [
                'id' => $mainBanner->getId(),
            ]);

            $this->em->flush();

            $pageMainBannerDeleteCount = $this->em
                ->getRepository(Entity\PageMainBanner::class)
                ->deleteByMainBanner($mainBanner);

            $this->logger->debug('Delete "PageMainBanner"', ['count' => $pageMainBannerDeleteCount]);

            $theaterMainBannerDeleteCount = $this->em
                ->getRepository(Entity\TheaterMainBanner::class)
                ->deleteByMainBanner($mainBanner);

            $this->logger->debug('Delete "TheaterMainBanner"', ['count' => $theaterMainBannerDeleteCount]);

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
        /** @var Entity\Page[] $pages */
        $pages = $this->em
            ->getRepository(Entity\Page::class)
            ->findActive();

        /** @var Entity\Theater[] $theaters */
        $theaters = $this->em
            ->getRepository(Entity\Theater::class)
            ->findActive();

        return $this->render($response, 'main_banner/publication.html.twig', [
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

        $form = new Form\MainBannerPublicationForm($target, $this->em);
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            throw new LogicException('invalid parameters.');
        }

        $cleanData       = $form->getData();
        $targetEntity    = null;
        $basePublication = null;

        if ($target === Form\MainBannerPublicationForm::TARGET_TEATER) {
            /** @var Entity\Theater $targetEntity */
            $targetEntity    = $this->em
                ->getRepository(Entity\Theater::class)
                ->findOneById((int) $cleanData['theater_id']);
            $basePublication = new Entity\TheaterMainBanner();
            $basePublication->setTheater($targetEntity);
        } elseif ($target === Form\MainBannerPublicationForm::TARGET_PAGE) {
            /** @var Entity\Page $targetEntity */
            $targetEntity    = $this->em
                ->getRepository(Entity\Page::class)
                ->findOneById((int) $cleanData['page_id']);
            $basePublication = new Entity\PageMainBanner();
            $basePublication->setPage($targetEntity);
        }

        // いったん削除する
        $targetEntity->getMainBanners()->clear();

        foreach ($cleanData['main_banners'] as $mainBannerData) {
            $publication = clone $basePublication;

            /** @var Entity\MainBanner|null $mainBanner */
            $mainBanner = $this->em
                ->getRepository(Entity\MainBanner::class)
                ->findOneById((int) $mainBannerData['main_banner_id']);

            if (! $mainBanner) {
                // @todo formで検証したい
                throw new LogicException('invalid main_banner.');
            }

            $publication->setMainBanner($mainBanner);
            $publication->setDisplayOrder((int) $mainBannerData['display_order']);

            $this->em->persist($publication);
        }

        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('%sの表示順を保存しました。', $targetEntity->getNameJa()),
        ]);

        $this->redirect($this->router->pathFor('main_banner_publication'), 303);
    }
}
