<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form;
use App\ORM\Entity;
use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class TheaterMetaController extends BaseController
{
    /**
     * opening hour action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeOpeningHour(Request $request, Response $response, array $args): Response
    {
        $user       = $this->auth->getUser();
        $repository = $this->em->getRepository(Entity\TheaterMeta::class);

        if ($user->isTheater()) {
            $metas = [$repository->findOneByTheaterId($user->getTheater()->getId())];
        } else {
            $metas = $repository->findActive();
        }

        return $this->render($response, 'theater_meta/opening_hour/list.html.twig', ['metas' => $metas]);
    }

    protected function renderEdit(Response $response, array $data = []): Response
    {
        return $this->render($response, 'theater_meta/opening_hour/edit.html.twig', $data);
    }

    /**
     * opening hour edit action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeOpeningHourEdit(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Theater|null $theater */
        $theater = $this->em
            ->getRepository(Entity\Theater::class)
            ->findOneById((int) $args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form\TheaterOpeningHourForm();

        $values = [
            'hours' => [],
        ];

        foreach ($theater->getMeta()->getOpeningHours() as $hour) {
            /** @var Entity\TheaterOpeningHour $hour */
            $values['hours'][] = [
                'type'      => $hour->getType(),
                'from_date' => $hour->getFromDate()->format('Y/m/d'),
                'to_date'   => $hour->getToDate() ? $hour->getToDate()->format('Y/m/d') : null,
                'time'      => $hour->getTime()->format('H:i'),
            ];
        }

        return $this->renderEdit($response, [
            'theater' => $theater,
            'form' => $form,
            'values' => $values,
        ]);
    }

    /**
     * opening hour update action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeOpeningHourUpdate(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Theater|null $theater */
        $theater = $this->em
            ->getRepository(Entity\Theater::class)
            ->findOneById((int) $args['id']);

        if (is_null($theater)) {
            throw new NotFoundException($request, $response);
        }

        $form = new Form\TheaterOpeningHourForm();
        $form->setData($request->getParams());

        if (! $form->isValid()) {
            return $this->renderEdit($response, [
                'theater' => $theater,
                'form' => $form,
                'values' => $request->getParams(),
                'errors' => $form->getMessages(),
                'is_validated' => true,
            ]);
        }

        $cleanData    = $form->getData();
        $openingHours = [];

        foreach ($cleanData['hours'] as $hourValues) {
            $openingHours[] = Entity\TheaterOpeningHour::create($hourValues);
        }

        $theater->getMeta()->setOpeningHours($openingHours);

        $this->em->flush();

        $this->flash->addMessage('alerts', [
            'type'    => 'info',
            'message' => sprintf('「%s」の開館時間を編集しました。', $theater->getNameJa()),
        ]);

        $this->redirect(
            $this->router->pathFor('opening_hour_edit', ['id' => $theater->getId()]),
            303
        );
    }
}
