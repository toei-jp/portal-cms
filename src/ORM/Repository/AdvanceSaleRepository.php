<?php

/**
 * AdvanceSaleRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Toei\PortalAdmin\ORM\Entity\AdvanceSale;
use Toei\PortalAdmin\Pagination\DoctrinePaginator;

/**
 * AdvanceSale repository class
 */
class AdvanceSaleRepository extends EntityRepository
{
    /**
     * find one by id
     *
     * @param int $id
     * @return AdvanceSale|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('sale');
        $qb
            ->where('sale.id = :id')
            ->andWhere('sale.isDeleted = false')
            ->setParameter('id', $id);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}
