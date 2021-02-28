<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Theater;
use Doctrine\ORM\EntityRepository;

class TheaterRepository extends EntityRepository
{
    /**
     * @return Theater[]
     */
    public function findActive(): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->orderBy('t.displayOrder', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int[] $ids
     * @return Theater[]
     */
    public function findByIds(array $ids): array
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.isDeleted = false')
            ->andWhere('t.id IN (:ids)')
            ->orderBy('t.displayOrder', 'ASC')
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?Theater
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->where('t.id = :id')
            ->andWhere('t.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
