<?php

namespace Toei\PortalAdmin\Controller;

use Toei\PortalAdmin\Exception\ForbiddenException;
use Toei\PortalAdmin\Form;
use Toei\PortalAdmin\ORM\Entity;
use Slim\Exception\NotFoundException;

/**
 * AdvanceTicket controller
 */
class AdvanceTicketController extends BaseController
{
    /**
     * return entity
     *
     * @param int $id
     * @return Entity\AdvanceSale|null
     */
    protected function getEntity(int $id): ?Entity\AdvanceSale
    {
        return $this->em
            ->getRepository(Entity\AdvanceSale::class)
            ->findOneById($id);
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

        $form = new Form\AdvanceTicketFindForm();
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
            $cleanValues['theater'] = [$user->getTheater()->getId()];
        }

        $this->data->set('form', $form);
        $this->data->set('values', $values);
        $this->data->set('params', $cleanValues);

        /** @var \Toei\PortalAdmin\Pagination\DoctrinePaginator $pagenater */
        $pagenater = $this->em->getRepository(Entity\AdvanceTicket::class)->findForList($cleanValues, $page);

        $this->data->set('pagenater', $pagenater);
    }

    /**
     * return form
     *
     * @param int $type
     * @return Form\AdvanceSaleForm|Form\AdvanceSaleForTheaterUserForm
     *
     * @throws \LogicException
     */
    protected function getForm(int $type)
    {
        if ($this->auth->getUser()->isTheater()) {
            if ($type === Form\AbstractAdvanceSaleForm::TYPE_NEW) {
                throw new \LogicException('Type "NEW" does not exist.');
            }

            return new Form\AdvanceSaleForTheaterUserForm($this->em);
        }

        return new Form\AdvanceSaleForm($type, $this->em);
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
        if ($this->auth->getUser()->isTheater()) {
            throw new ForbiddenException();
        }

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_NEW);
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
        if ($this->auth->getUser()->isTheater()) {
            throw new ForbiddenException();
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_NEW);
        $form->setData($params);

        if (! $form->isValid()) {
            $this->data->set('form', $form);
            $this->data->set('values', $params);
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'new';
        }

        $advanceSale = $this->doCreate($form);

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を追加しました。',
        ]);

        $this->redirect(
            $this->router->pathFor('advance_ticket_edit', ['id' => $advanceSale->getId()]),
            303
        );
    }

    /**
     * do create
     *
     * @param Form\AdvanceSaleForm $form
     * @return Entity\AdvanceSale
     */
    protected function doCreate(Form\AdvanceSaleForm $form): Entity\AdvanceSale
    {
        $cleanData = $form->getData();

        $advanceSale = new Entity\AdvanceSale();

        /** @var Entity\Theater $theater */
        $theater = $this->em
            ->getRepository(Entity\Theater::class)
            ->findOneById($cleanData['theater']);
        $advanceSale->setTheater($theater);

        /** @var Entity\Title $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById($cleanData['title_id']);
        $advanceSale->setTitle($title);

        $advanceSale->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $advanceSale->setPublishingExpectedDateText($cleanData['publishing_expected_date_text']);
        $advanceSale->setCreatedUser($this->auth->getUser());
        $advanceSale->setUpdatedUser($this->auth->getUser());

        $this->em->persist($advanceSale);

        foreach ($cleanData['tickets'] as $ticket) {
            $advanceTicket = new Entity\AdvanceTicket();
            $advanceTicket->setAdvanceSale($advanceSale);
            $advanceTicket->setReleaseDt($ticket['release_dt']);
            $advanceTicket->setReleaseDtText($ticket['release_dt_text']);
            $advanceTicket->setIsSalesEnd($ticket['is_sales_end'] === '1');
            $advanceTicket->setType($ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock($ticket['special_gift_stock']);

            $image = $ticket['special_gift_image'];
            $file  = null;

            if ($image['name']) {
                $file = $this->uploadFile($image);

                $this->em->persist($file);
            }

            $advanceTicket->setSpecialGiftImage($file);

            $this->em->persist($advanceTicket);
        }

        $this->em->flush();

        return $advanceSale;
    }

    /**
     * upload file
     *
     * @param array $uploadFile
     * @return Entity\File
     */
    protected function uploadFile(array $uploadFile): Entity\File
    {
        $newName = Entity\File::createName($uploadFile['name']);

        // upload storage
        $options = new \MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions();
        $options->setContentType($uploadFile['type']);
        $this->bc->createBlockBlob(
            Entity\File::getBlobContainer(),
            $newName,
            fopen($uploadFile['tmp_name'], 'r'),
            $options
        );

        $file = new Entity\File();
        $file->setName($newName);
        $file->setOriginalName($uploadFile['name']);
        $file->setMimeType($uploadFile['type']);
        $file->setSize((int) $uploadFile['size']);

        return $file;
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
        $advanceSale = $this->getEntity($args['id']);

        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }

        $this->data->set('advanceSale', $advanceSale);

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_EDIT);
        $this->data->set('form', $form);

        $values = $this->entityToArray($advanceSale);
        $this->data->set('values', $values);
    }

    /**
     * AdvanceSale entity to array
     *
     * @param Entity\AdvanceSale $advanceSale
     * @return array
     */
    protected function entityToArray(Entity\AdvanceSale $advanceSale): array
    {
        $values = [
            'id'         => $advanceSale->getId(),
            'theater'    => $advanceSale->getTheater()->getId(),
            'title_id'   => $advanceSale->getTitle()->getId(),
            'title_name' => $advanceSale->getTitle()->getName(),
            'publishing_expected_date_text' => $advanceSale->getPublishingExpectedDateText(),
            'tickets'    => [],
        ];

        $publishingExpectedDate = $advanceSale->getPublishingExpectedDate();

        if ($publishingExpectedDate instanceof \DateTime) {
            $values['publishing_expected_date']           = $publishingExpectedDate->format('Y/m/d');
            $values['not_exist_publishing_expected_date'] = null;
        } else {
            $values['publishing_expected_date']           = null;
            $values['not_exist_publishing_expected_date'] = '1';
        }

        foreach ($advanceSale->getActiveAdvanceTickets() as $advanceTicket) {
            /** @var Entity\AdvanceTicket $advanceTicket */
            $ticket = [
                'id'                 => $advanceTicket->getId(),
                'release_dt'         => $advanceTicket->getReleaseDt()->format('Y/m/d H:i'),
                'release_dt_text'    => $advanceTicket->getReleaseDtText(),
                'is_sales_end'       => $advanceTicket->getIsSalesEnd() ? '1' : '0',
                'type'               => $advanceTicket->getType(),
                'price_text'         => $advanceTicket->getPriceText(),
                'special_gift'       => $advanceTicket->getSpecialGift(),
                'special_gift_stock' => $advanceTicket->getSpecialGiftStock(),
            ];

            $values['tickets'][] = $ticket;
        }

        return $values;
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
        $advanceSale = $this->getEntity($args['id']);

        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            $this->data->set('advanceSale', $advanceSale);
            $this->data->set('form', $form);
            $this->data->set('values', $params);
            $this->data->set('errors', $form->getMessages());
            $this->data->set('is_validated', true);

            return 'edit';
        }

        if ($this->auth->getUser()->isTheater()) {
            $this->doUpdateForTheaterUser($form, $advanceSale);
        } else {
            $this->doUpdate($form, $advanceSale);
        }

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を編集しました。',
        ]);

        $this->redirect(
            $this->router->pathFor('advance_ticket_edit', ['id' => $advanceSale->getId()]),
            303
        );
    }

    /**
     * do update
     *
     * @param Form\AdvanceSaleForm $form
     * @param Entity\AdvanceSale   $advanceSale
     */
    protected function doUpdate(Form\AdvanceSaleForm $form, Entity\AdvanceSale $advanceSale)
    {
        $cleanData = $form->getData();

        /** @var Entity\Theater $theater */
        $theater = $this->em
            ->getRepository(Entity\Theater::class)
            ->findOneById($cleanData['theater']);
        $advanceSale->setTheater($theater);

        /** @var Entity\Title $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById($cleanData['title_id']);
        $advanceSale->setTitle($title);

        $advanceSale->setPublishingExpectedDate($cleanData['publishing_expected_date']);
        $advanceSale->setPublishingExpectedDateText($cleanData['publishing_expected_date_text']);
        $advanceSale->setUpdatedUser($this->auth->getUser());

        $advanceTickets = $advanceSale->getActiveAdvanceTickets();

        if (is_array($cleanData['delete_tickets'])) {
            // 前売券削除

            foreach ($cleanData['delete_tickets'] as $advanceTicketId) {
                /**
                 * indexByでidをindexにしている
                 *
                 * @var Entity\AdvanceTicket|null $advanceTicket
                 */
                $advanceTicket = $advanceTickets->get($advanceTicketId);

                if (
                    ! $advanceTicket
                    || $advanceTicket->getId() !== (int) $advanceTicketId // 念のため確認
                ) {
                    throw new \RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $advanceTicketId));
                }

                $advanceTicket->setIsDeleted(true);
            }
        }

        foreach ($cleanData['tickets'] as $ticket) {
            if ($ticket['id']) {
                // 前売券編集

                /**
                 * indexByでidをindexにしている
                 *
                 * @var Entity\AdvanceTicket|null $advanceTicket
                 */
                $advanceTicket = $advanceTickets->get($ticket['id']);

                if (
                    ! $advanceTicket
                    || $advanceTicket->getId() !== (int) $ticket['id'] // 念のため確認
                ) {
                    throw new \RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $ticket['id']));
                }
            } else {
                // 前売券登録
                $advanceTicket = new Entity\AdvanceTicket();
                $this->em->persist($advanceTicket);

                $advanceTicket->setAdvanceSale($advanceSale);
            }

            $advanceTicket->setReleaseDt($ticket['release_dt']);
            $advanceTicket->setReleaseDtText($ticket['release_dt_text']);
            $advanceTicket->setIsSalesEnd($ticket['is_sales_end'] === '1');
            $advanceTicket->setType($ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock($ticket['special_gift_stock']);

            $image         = $ticket['special_gift_image'];
            $isDeleteImage = ($ticket['delete_special_gift_image'] == '1') || $image['name'];

            if ($isDeleteImage && $advanceTicket->getSpecialGiftImage()) {
                // @todo preUpdateで出来ないか？ hasChangedField()
                $oldImage = $advanceTicket->getSpecialGiftImage();
                $this->em->remove($oldImage);

                // @todo postRemoveイベントへ
                $this->bc->deleteBlob(Entity\File::getBlobContainer(), $oldImage->getName());

                $advanceTicket->setSpecialGiftImage(null);
            }

            if (! $image['name']) {
                continue;
            }

            $file = $this->uploadFile($image);

            $this->em->persist($file);

            $advanceTicket->setSpecialGiftImage($file);
        }

        $this->em->flush();
    }

    /**
     * do update for theater user
     *
     * @param Form\AdvanceSaleForTheaterUserForm $form
     * @param Entity\AdvanceSale                 $advanceSale
     * @return void
     */
    protected function doUpdateForTheaterUser(Form\AdvanceSaleForTheaterUserForm $form, Entity\AdvanceSale $advanceSale)
    {
        $cleanData = $form->getData();

        $advanceSale->setUpdatedUser($this->auth->getUser());

        $advanceTickets = $advanceSale->getActiveAdvanceTickets();

        foreach ($cleanData['tickets'] as $ticket) {
            // 前売券編集

            /**
             * indexByでidをindexにしている
             *
             * @var Entity\AdvanceTicket|null $advanceTicket
             */
            $advanceTicket = $advanceTickets->get($ticket['id']);

            if (
                ! $advanceTicket
                || $advanceTicket->getId() !== (int) $ticket['id'] // 念のため確認
            ) {
                throw new \RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $ticket['id']));
            }

            $advanceTicket->setSpecialGiftStock($ticket['special_gift_stock']);
        }

        $this->em->flush();
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
        /** @var Entity\AdvanceTicket|null $advanceTicket */
        $advanceTicket = $this->em->getRepository(Entity\AdvanceTicket::class)->findOneById($args['id']);

        if (is_null($advanceTicket)) {
            throw new NotFoundException($request, $response);
        }

        // 関連データの処理はイベントで対応する
        $advanceTicket->setIsDeleted(true);

        $advanceSale = $advanceTicket->getAdvanceSale();
        $advanceSale->setUpdatedUser($this->auth->getUser());

        // 有効なAdvanceTicketの件数確認
        if ($advanceSale->getActiveAdvanceTickets()->count() === 1) {
            // この処理で有効なAdvanceTicketが無くなるのでAdvanceSaleも削除する
            $advanceSale->setIsDeleted(true);
        }

        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => '前売券情報を削除しました。',
        ]);

        $this->redirect($this->router->pathFor('advance_ticket_list'), 303);
    }
}
