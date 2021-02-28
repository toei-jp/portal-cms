<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\Traits\AzureBlobStorage;
use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;

class NewsController extends BaseController
{
    use AzureBlobStorage;

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $headline = $request->getParam('headline');
        $data     = [];

        if (! empty($headline)) {
            /** @var Entity\News[] $newsList */
            $newsList = $this->em
                ->getRepository(Entity\News::class)
                ->findForListApi($headline);

            foreach ($newsList as $news) {
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

        return $response->withJson(['data' => $data]);
    }
}
