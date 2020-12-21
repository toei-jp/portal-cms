<?php

/**
 * TitleController.php
 */

namespace Toei\PortalAdmin\Controller\API;

use Toei\PortalAdmin\ORM\Entity;

/**
 * Title API controller
 */
class TitleController extends BaseController
{
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
        $name = $request->getParam('name');
        $data = [];

        if (! empty($name)) {
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForListApi($name);

            foreach ($titles as $title) {
                /** @var Entity\Title $title */

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

        $this->data->set('data', $data);
    }

    /**
     * find imported title action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeFindImported($request, $response, $args)
    {
        $ids  = json_decode($request->getParam('ids'));
        $data = [];

        if ($ids !== null) {
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForFindImportedApi($ids);

            foreach ($titles as $title) {
                /** @var Entity\Title $title */

                array_push($data, $title->getCheverCode());
            }
        }

        $this->data->set('data', $data);
    }

    /**
     * import title action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeImportTitles($request, $response, $args)
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
            } catch (\Exception $e) {
                $this->logger->error($e);
                array_push($errors, $e->getMessage());
            }
        }
        if (count($errors) > 0) {
            $this->data->set('status', 'fail');
            $this->data->set('errors', $errors);
        } else {
            $this->data->set('status', 'success');
        }
    }

    /**
     * autocomplete action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeAutocomplete($request, $response, $args)
    {
        $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForAutocomplete($request->getParams());

        $data = [];

        foreach ($titles as $title) {
            /** @var Entity\Title $title */

            $data[] = [
                'name'      => $title->getName(),
                'name_kana' => $title->getNameKana(),
                'sub_title' => $title->getSubTitle(),
            ];
        }

        $this->data->set('data', $data);
    }
}
