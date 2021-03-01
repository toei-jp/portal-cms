<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\ImageResize;
use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use App\Pagination\DoctrinePaginator;
use DateTime;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class TitleController extends BaseController
{
    use ImageResize;

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

        $form = new Form\TitleFindForm();
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
            ->getRepository(Entity\Title::class)
            ->findForList($cleanValues, $page);

        return $this->render($response, 'title/list.html.twig', [
            'page' => $page,
            'values' => $values,
            'params' => $cleanValues,
            'errors' => $errors,
            'pagenater' => $pagenater,
        ]);
    }

    /**
     * import action
     *
     * @param array<string, mixed> $args
     */
    public function executeImport(Request $request, Response $response, array $args): Response
    {
        return $this->render($response, 'title/import.html.twig');
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderNew(Response $response, array $data = []): Response
    {
        return $this->render($response, 'title/new.html.twig', $data);
    }

    /**
     * new action
     *
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        $form = new Form\TitleForm();

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

        $form = new Form\TitleForm();
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
        $file  = null;

        if ($image['name']) {
            // rename
            $newName = Entity\File::createName($image['name']);

            // TOEI-150
            $imageStream = $this->resizeImage($image['tmp_name'], 1280);

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
        }

        $title = new Entity\Title();
        $title->setImage($file);
        $title->setName($cleanData['name']);
        $title->setNameKana($cleanData['name_kana']);
        $title->setSubTitle($cleanData['sub_title']);
        $title->setCredit($cleanData['credit']);
        $title->setCatchcopy($cleanData['catchcopy']);
        $title->setIntroduction($cleanData['introduction']);
        $title->setDirector($cleanData['director']);
        $title->setCast($cleanData['cast']);
        $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $title->setOfficialSite($cleanData['official_site']);
        $title->setRating((int) $cleanData['rating']);
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setCreatedUser($this->auth->getUser());
        $title->setUpdatedUser($this->auth->getUser());

        $this->em->persist($title);
        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を追加しました。', $title->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('title_edit', ['id' => $title->getId()]),
            303
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data = []): Response
    {
        return $this->render($response, 'title/edit.html.twig', $data);
    }

    /**
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Title|null $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form\TitleForm();

        $values = [
            'id'            => $title->getId(),
            'name'          => $title->getName(),
            'name_kana'     => $title->getNameKana(),
            'sub_title'     => $title->getSubTitle(),
            'credit'        => $title->getCredit(),
            'catchcopy'     => $title->getCatchcopy(),
            'introduction'  => $title->getIntroduction(),
            'director'      => $title->getDirector(),
            'cast'          => $title->getCast(),
            'official_site' => $title->getOfficialSite(),
            'rating'        => $title->getRating(),
            'universal'     => $title->getUniversal(),
        ];

        $publishingExpectedDate = $title->getPublishingExpectedDate();

        if ($publishingExpectedDate instanceof DateTime) {
            $values['publishing_expected_date']           = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date']           = null;
            $values['not_exist_publishing_expected_date'] = '1';
        }

        return $this->renderEdit($response, [
            'title' => $title,
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
        /** @var Entity\Title|null $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = new Form\TitleForm();
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'title' => $title,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData = $form->getData();

        $image         = $cleanData['image'];
        $isDeleteImage = $cleanData['delete_image'] || $image['name'];

        if ($isDeleteImage && $title->getImage()) {
            // @todo preUpdateで出来ないか？ hasChangedField()
            $oldImage = $title->getImage();
            $this->em->remove($oldImage);

            // @todo postRemoveイベントへ
            $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());

            $title->setImage(null);
        }

        if ($image['name']) {
            // rename
            $newName = Entity\File::createName($image['name']);

            // TOEI-150
            $imageStream = $this->resizeImage($image['tmp_name'], 1280);

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

            $title->setImage($file);
        }

        $title->setName($cleanData['name']);
        $title->setNameKana($cleanData['name_kana']);
        $title->setSubTitle($cleanData['sub_title']);
        $title->setCredit($cleanData['credit']);
        $title->setCatchcopy($cleanData['catchcopy']);
        $title->setIntroduction($cleanData['introduction']);
        $title->setDirector($cleanData['director']);
        $title->setCast($cleanData['cast']);
        $title->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $title->setOfficialSite($cleanData['official_site']);
        $title->setRating((int) $cleanData['rating']);
        $title->setUniversal($cleanData['universal'] ?? []);
        $title->setUpdatedUser($this->auth->getUser());
        $title->setIsDeleted(false);

        $this->em->persist($title);
        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を編集しました。', $title->getName()),
        ]);

        $this->redirect(
            $this->router->pathFor('title_edit', ['id' => $title->getId()]),
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
        /** @var Entity\Title|null $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $args['id']);

        if (is_null($title)) {
            throw new NotFoundException($request, $response);
        }

        $title->setIsDeleted(true);
        $title->setUpdatedUser($this->auth->getUser());

        // 関連データの処理はイベントで対応する

        $this->em->persist($title);
        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('作品「%s」を削除しました。', $title->getName()),
        ]);

        $this->redirect($this->router->pathFor('title_list'), 303);
    }
}
