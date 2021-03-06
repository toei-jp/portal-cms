<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\Form\ScheduleFindForm;
use App\ORM\Entity\Schedule;
use App\Pagination\DoctrinePaginator;
use Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository
{
    /**
     * @param array<string, mixed> $params
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.isDeleted = false')
            ->orderBy('s.createdAt', 'DESC');

        if (isset($params['title_name']) && ! empty($params['title_name'])) {
            $qb
                ->join('s.title', 't')
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('t.name', ':name'),
                    $qb->expr()->like('t.nameKana', ':name'),
                    $qb->expr()->like('t.subTitle', ':name')
                ))
                ->setParameter('name', '%' . $params['title_name'] . '%');
        }

        if (isset($params['status']) && ! empty($params['status'])) {
            $or = $qb->expr()->orX();

            if (in_array(ScheduleFindForm::STATUS_SHOWING, $params['status'])) {
                $or->add($qb->expr()->andX(
                    $qb->expr()->lte('s.startDate', 'CURRENT_DATE()'),
                    $qb->expr()->gte('s.endDate', 'CURRENT_DATE()')
                ));
            }

            if (in_array(ScheduleFindForm::STATUS_BEFORE, $params['status'])) {
                $or->add(
                    $qb->expr()->gt('s.startDate', 'CURRENT_DATE()')
                );
            }

            if (in_array(ScheduleFindForm::STATUS_END, $params['status'])) {
                $or->add(
                    $qb->expr()->lt('s.endDate', 'CURRENT_DATE()')
                );
            }

            $qb->andWhere($or);
        }

        if (isset($params['format_system']) && ! empty($params['format_system'])) {
            $qb
                ->join('s.showingFormats', 'sf')
                ->andWhere($qb->expr()->in('sf.system', $params['format_system']));
        }

        if (isset($params['public_start_dt']) && ! empty($params['public_start_dt'])) {
            $qb
                ->andWhere('s.publicStartDt = :public_start_dt')
                ->setParameter('public_start_dt', $params['public_start_dt']);
        }

        if (isset($params['public_end_dt']) && ! empty($params['public_end_dt'])) {
            $qb
                ->andWhere('s.publicEndDt = :public_end_dt')
                ->setParameter('public_end_dt', $params['public_end_dt']);
        }

        $query = $qb->getQuery();

        return new DoctrinePaginator($query, $page, $maxPerPage);
    }

    public function findOneById(int $id): ?Schedule
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->where('s.id = :id')
            ->andWhere('s.isDeleted = false')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
