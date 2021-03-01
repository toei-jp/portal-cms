<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\MainBanner;
use Doctrine\ORM\EntityRepository;

class PageMainBannerRepository extends EntityRepository
{
    public function deleteByMainBanner(MainBanner $mainBanner): int
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
