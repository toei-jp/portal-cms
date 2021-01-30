<?php

namespace App\Controller\API;

use App\Controller\Traits\AzureBlobStorage;
use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * News API controller
 */
class NewsController extends BaseController
{
    use AzureBlobStorage;

    /**
     * list action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return string|void
     */
    public function executeList($request, $response, $args)
    {
        $headline = $request->getParam('headline');
        $data     = [];

        if (! empty($headline)) {
            $newsList = $this->em
                ->getRepository(Entity\News::class)
                ->findForListApi($headline);

            foreach ($newsList as $news) {
                /** @var Entity\News $news */

                $data[] = [
                    'id'             => $news->getId(),
                    'headline'       => $news->getHeadline(),
                    'image'          => $this->getBlobUrl(
                        Entity\File::getBlobContainer(),
                        $news->getImage()->getName()
                    ),
                    'category_label' => $news->getCategoryLabel(),
                ];
            }
        }

        $this->data->set('data', $data);
    }
}
