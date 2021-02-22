<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;
use Throwable;

class TitleController extends BaseController
{
    /**
     * list action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $name = $request->getParam('name');
        $data = [];

        if (! empty($name)) {
            /** @var Entity\Title[] $titles */
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForListApi($name);

            foreach ($titles as $title) {
                $data[] = [
                    'id'            => $title->getId(),
                    'name'          => $title->getName(),
                    'official_site' => $title->getOfficialSite(),
                    'publishing_expected_date' => $title->getPublishingExpectedDate()
                        ? $title->getPublishingExpectedDate()->format('Y/m/d')
                        : null,
                ];
            }
        }

        return $response->withJson(['data' => $data]);
    }

    /**
     * find imported title action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeFindImported(Request $request, Response $response, array $args): Response
    {
        $ids  = json_decode($request->getParam('ids'));
        $data = [];

        if ($ids !== null) {
            /** @var Entity\Title[] $titles */
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForFindImportedApi($ids);

            foreach ($titles as $title) {
                array_push($data, $title->getCheverCode());
            }
        }

        return $response->withJson(['data' => $data]);
    }

    /**
     * import title action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeImportTitles(Request $request, Response $response, array $args): Response
    {
        $data   = $request->getParams();
        $errors = [];

        foreach ($data['titles'] as $title) {
            $ratings = Entity\Title::getRatingTypes();
            $i       = array_search($title['rating'], $ratings);

            if ($i !== false) {
                $title['rating'] = $i;
            } else {
                $title['rating'] = null;
            }

            try {
                $newTitle = new Entity\Title();
                $newTitle->setName($title['name']);

                if (! empty($title['sub_title'])) {
                    $newTitle->setSubTitle($title['sub_title']);
                }

                $newTitle->setCheverCode($title['chever_code']);

                if (
                    ! isset($title['not_exist_publishing_expected_date']) ||
                    $title['not_exist_publishing_expected_date'] !== '1'
                ) {
                    $newTitle->setPublishingExpectedDate($title['publishing_expected_date']);
                }

                $newTitle->setRating((int) $title['rating']);
                $newTitle->setUniversal([]);
                $newTitle->setCreatedUser($this->auth->getUser());
                $newTitle->setUpdatedUser($this->auth->getUser());

                $this->em->persist($newTitle);
                $this->em->flush();
            } catch (Throwable $e) {
                $this->logger->error($e->getMessage());
                array_push($errors, $e->getMessage());
            }
        }

        if (count($errors) > 0) {
            return $response->withJson([
                'status' => 'fail',
                'errors' => $errors,
            ]);
        }

        return $response->withJson(['status' => 'success']);
    }

    /**
     * autocomplete action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeAutocomplete(Request $request, Response $response, array $args): Response
    {
        /** @var Entity\Title[] $titles */
        $titles = $this->em
            ->getRepository(Entity\Title::class)
            ->findForAutocomplete($request->getParams());

        $data = [];

        foreach ($titles as $title) {
            $data[] = [
                'name'      => $title->getName(),
                'name_kana' => $title->getNameKana(),
                'sub_title' => $title->getSubTitle(),
            ];
        }

        return $response->withJson(['data' => $data]);
    }
}
