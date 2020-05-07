<?php

/**
 * TheaterMainBannerRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Toei\PortalAdmin\ORM\Entity\MainBanner;
use Toei\PortalAdmin\ORM\Entity\TheaterMainBanner;

/**
 * TheaterMainBanner repository class
 */
class TheaterMainBannerRepository extends EntityRepository
{
    /**
     * delete by MainBanner
     *
     * @param MainBanner $mainBanner
     * @return int
     */
    public function deleteByMainBanner(MainBanner $mainBanner)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->delete($this->getEntityName(), 'tm')
            ->where('tm.mainBanner = :main_banner')
            ->setParameter('main_banner', $mainBanner->getId())
            ->getQuery();
        
        return $query->execute();
    }
}
