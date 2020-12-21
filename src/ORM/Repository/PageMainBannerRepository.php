<?php

/**
 * PageMainBannerRepository.php
 */

namespace Toei\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Toei\PortalAdmin\ORM\Entity\MainBanner;
use Toei\PortalAdmin\ORM\Entity\PageMainBanner;

/**
 * PageMainBanner repository class
 */
class PageMainBannerRepository extends EntityRepository
{
    /**
     * delete by MainBanner
     *
     * @param MainBanner $mainBanner
     * @return int
     */
    public function deleteByMainBanner(MainBanner $mainBanner)
    {
        $qb    = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->delete($this->getEntityName(), 'pm')
            ->where('pm.mainBanner = :main_banner')
            ->setParameter('main_banner', $mainBanner->getId())
            ->getQuery();

        return $query->execute();
    }
}
