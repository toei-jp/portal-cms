<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Page;
use Doctrine\ORM\EntityRepository;

class PageRepository extends EntityRepository
{
    /**
     * @return Page[]
     */
    public function findActive(): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.isDeleted = false');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $ids
     * @return Page[]
     */
    public function findByIds(array $ids): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.isDeleted = false')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?Page
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.id = :id')
            ->andWhere('p.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
