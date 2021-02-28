<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\News;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

class NewsRepository extends EntityRepository
{
    /**
     * @param array<string, mixed> $params
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->where('n.isDeleted = false')
            ->orderBy('n.createdAt', 'DESC');

        if (isset($params['user'])) {
            $qb
                ->andWhere('n.createdUser = :user')
                ->setParameter('user', $params['user']);
        }

        if (isset($params['term']) && count($params['term']) > 0) {
            $or = $qb->expr()->orX();

            if (in_array('1', $params['term'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->lte('n.startDt', 'CURRENT_TIMESTAMP()'),
                    $qb->expr()->gt('n.endDt', 'CURRENT_TIMESTAMP()')
                ));
            }

            if (in_array('2', $params['term'])) {
                $or->add($qb->expr()->lte('n.endDt', 'CURRENT_TIMESTAMP()'));
            }

            $qb->andWhere($or);
        }

        if (isset($params['category'])) {
            $qb
                ->andWhere('n.category = :category')
                ->setParameter('category', $params['category']);
        }

        if (isset($params['page']) && count($params['page']) > 0) {
            $qb
                ->join('n.pages', 'np')
                ->andWhere($qb->expr()->in('np.page', $params['page']));
        }

        if (isset($params['theater']) && count($params['theater']) > 0) {
            $qb
                ->join('n.theaters', 'nt')
                ->andWhere($qb->expr()->in('nt.theater', $params['theater']));
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    /**
     * @return News[]
     */
    public function findForListApi(string $headline): array
    {
        if (empty($headline)) {
            throw new InvalidArgumentException('invalid "headline".');
        }

        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.isDeleted = false')
            ->andWhere('c.headline LIKE :headline')
            ->andWhere('c.endDt > CURRENT_TIMESTAMP()')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('headline', '%' . $headline . '%');

        return $qb->getQuery()->getResult();
    }

    public function findOneById(int $id): ?News
    {
        $qb = $this->createQueryBuilder('n');
        $qb
            ->where('n.id = :id')
            ->andWhere('n.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
