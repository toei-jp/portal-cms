<?php

/**
 * MainBannerController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Controller\API;

use Toei\PortalAdmin\Controller\Traits\AzureBlobStorage;
use Toei\PortalAdmin\ORM\Entity;

/**
 * MainBanner API controller
 */
class MainBannerController extends BaseController
{
    use AzureBlobStorage;

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

        if (!empty($name)) {
            $mainBannerList = $this->em
                ->getRepository(Entity\MainBanner::class)
                ->findForListApi($name);

            foreach ($mainBannerList as $mainBanner) {
                /** @var Entity\MainBanner $mainBanner */

                $data[] = [
                    'id'    => $mainBanner->getId(),
                    'name'  => $mainBanner->getName(),
                    'image' => $this->getBlobUrl(
                        Entity\File::getBlobContainer(),
                        $mainBanner->getImage()->getName()
                    ),
                ];
            }
        }

        $this->data->set('data', $data);
    }
}
