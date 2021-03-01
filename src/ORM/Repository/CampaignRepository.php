<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\Campaign;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

class CampaignRepository extends EntityRepository
{
    /**
     * @param array<string, mixed> $params
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.isDeleted = false')
            ->orderBy('c.createdAt', 'DESC');

        if (isset($params['status']) && count($params['status']) > 0) {
            $or = $qb->expr()->orX();

            if (in_array('1', $params['status'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->lte('c.startDt', 'CURRENT_TIMESTAMP()'),
                    $qb->expr()->gt('c.endDt', 'CURRENT_TIMESTAMP()')
                ));
            }

            if (in_array('2', $params['status'])) {
                $or->add($qb->expr()->lte('c.endDt', 'CURRENT_TIMESTAMP()'));
            }

            $qb->andWhere($or);
        }

        if (isset($params['page']) && count($params['page']) > 0) {
            $qb
                ->join('c.pages', 'cp')
                ->andWhere($qb->expr()->in('cp.page', $params['page']));
        }

        if (isset($params['theater']) && count($params['theater']) > 0) {
            $qb
                ->join('c.theaters', 'ct')
                ->andWhere($qb->expr()->in('ct.theater', $params['theater']));
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * @return Campaign[]
     */
    public function findForListApi(string $name): array
    {
        if (empty($name)) {
            throw new InvalidArgumentException('invalid "name".');
        }

        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.isDeleted = false')
            ->andWhere('c.name LIKE :name')
            ->andWhere('c.endDt > CURRENT_TIMESTAMP()')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('name', '%' . $name . '%');

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?Campaign
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.id = :id')
            ->andWhere('c.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
