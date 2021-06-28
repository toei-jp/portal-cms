<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ForbiddenException;
use App\Form;
use App\ORM\Entity;
use App\Pagination\DoctrinePaginator;
use DateTime;
use LogicException;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use RuntimeException;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class AdvanceTicketController extends BaseController
{
    protected function getEntity(int $id): ?Entity\AdvanceSale
    {
        return $this->em
            ->getRepository(Entity\AdvanceSale::class)
            ->findOneById($id);
    }

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $page = (int) $request->getParam('p', 1);

        $form = new Form\AdvanceTicketFindForm();
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
            $cleanValues['theater'] = [$user->getTheater()->getId()];
        }

        /** @var DoctrinePaginator $pagenater */
        $pagenater = $this->em
            ->getRepository(Entity\AdvanceTicket::class)
            ->findForList($cleanValues, $page);

        return $this->render($response, 'advance_ticket/list.html.twig', [
            'form' => $form,
            'values' => $values,
            'params' => $cleanValues,
            'errors' => $errors,
            'pagenater' => $pagenater,
        ]);
    }

    /**
     * @return Form\AdvanceSaleForm|Form\AdvanceSaleForTheaterUserForm
     *
     * @throws LogicException
     */
    protected function getForm(int $type)
    {
        if ($this->auth->getUser()->isTheater()) {
            if ($type === Form\AbstractAdvanceSaleForm::TYPE_NEW) {
                throw new LogicException('Type "NEW" does not exist.');
            }

            return new Form\AdvanceSaleForTheaterUserForm($this->em);
        }

        return new Form\AdvanceSaleForm($type, $this->em);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function renderNew(Response $response, array $data): Response
    {
        return $this->render($response, 'advance_ticket/new.html.twig', $data);
    }

    /**
     * new action
     *
     * @param array<string, mixed> $args
     */
    public function executeNew(Request $request, Response $response, array $args): Response
    {
        if ($this->auth->getUser()->isTheater()) {
            throw new ForbiddenException();
        }

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_NEW);

        return $this->renderNew($response, ['form' => $form]);
    }

    /**
     * create action
     *
     * @param array<string, mixed> $args
     */
    public function executeCreate(Request $request, Response $response, array $args): Response
    {
        if ($this->auth->getUser()->isTheater()) {
            throw new ForbiddenException();
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_NEW);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderNew($response, [
                'form' => $form,
                'values' => $params,
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
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

    protected function doCreate(Form\AdvanceSaleForm $form): Entity\AdvanceSale
    {
        $cleanData = $form->getData();

        $advanceSale = new Entity\AdvanceSale();

        /** @var Entity\Theater $theater */
        $theater = $this->em
            ->getRepository(Entity\Theater::class)
            ->findOneById((int) $cleanData['theater']);
        $advanceSale->setTheater($theater);

        /** @var Entity\Title $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $cleanData['title_id']);
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
            $advanceTicket->setType((int) $ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock((int) $ticket['special_gift_stock'] ?: null);

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
     * @param array<string, mixed> $uploadFile
     */
    protected function uploadFile(array $uploadFile): Entity\File
    {
        $newName = Entity\File::createName($uploadFile['name']);

        // upload storage
        $options = new CreateBlockBlobOptions();
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
     * @param array<string, mixed> $data
     */
    protected function renderEdit(Response $response, array $data): Response
    {
        return $this->render($response, 'advance_ticket/edit.html.twig', $data);
    }

    /**
     * edit action
     *
     * @param array<string, mixed> $args
     */
    public function executeEdit(Request $request, Response $response, array $args): Response
    {
        $advanceSale = $this->getEntity((int) $args['id']);

        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_EDIT);

        $values = $this->entityToArray($advanceSale);

        return $this->renderEdit($response, [
            'advanceSale' => $advanceSale,
            'form' => $form,
            'values' => $values,
        ]);
    }

    /**
     * @return array<string, mixed>
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

        if ($publishingExpectedDate instanceof DateTime) {
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
     * @param array<string, mixed> $args
     */
    public function executeUpdate(Request $request, Response $response, array $args): Response
    {
        $advanceSale = $this->getEntity((int) $args['id']);

        if (is_null($advanceSale)) {
            throw new NotFoundException($request, $response);
        }

        // Laminas_Formの都合で$request->getUploadedFiles()ではなく$_FILESを使用する
        $params = Form\BaseForm::buildData($request->getParams(), $_FILES);

        $form = $this->getForm(Form\AbstractAdvanceSaleForm::TYPE_EDIT);
        $form->setData($params);

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'advanceSale' => $advanceSale,
                'form' => $form,
                'values' => $params,
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
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

    protected function doUpdate(Form\AdvanceSaleForm $form, Entity\AdvanceSale $advanceSale): void
    {
        $cleanData = $form->getData();

        /** @var Entity\Theater $theater */
        $theater = $this->em
            ->getRepository(Entity\Theater::class)
            ->findOneById((int) $cleanData['theater']);
        $advanceSale->setTheater($theater);

        /** @var Entity\Title $title */
        $title = $this->em
            ->getRepository(Entity\Title::class)
            ->findOneById((int) $cleanData['title_id']);
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
                    throw new RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $advanceTicketId));
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
                    throw new RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $ticket['id']));
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
            $advanceTicket->setType((int) $ticket['type']);
            $advanceTicket->setPriceText($ticket['price_text']);
            $advanceTicket->setSpecialGift($ticket['special_gift']);
            $advanceTicket->setSpecialGiftStock((int) $ticket['special_gift_stock'] ?: null);

            $image         = $ticket['special_gift_image'];
            $isDeleteImage = ($ticket['delete_special_gift_image'] === '1') || $image['name'];

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

    protected function doUpdateForTheaterUser(
        Form\AdvanceSaleForTheaterUserForm $form,
        Entity\AdvanceSale $advanceSale
    ): void {
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
                throw new RuntimeException(sprintf('advance_ticket(%s) dose not eixist.', $ticket['id']));
            }

            $advanceTicket->setSpecialGiftStock((int) $ticket['special_gift_stock']);
        }

        $this->em->flush();
    }

    /**
     * delete action
     *
     * @param array<string, mixed> $args
     */
    public function executeDelete(Request $request, Response $response, array $args): void
    {
        /** @var Entity\AdvanceTicket|null $advanceTicket */
        $advanceTicket = $this->em
            ->getRepository(Entity\AdvanceTicket::class)
            ->findOneById((int) $args['id']);

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
