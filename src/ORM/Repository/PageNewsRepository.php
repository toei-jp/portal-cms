<?php

declare(strict_types=1);

namespace App\ORM\Repository;

use App\ORM\Entity\News;
use Doctrine\ORM\EntityRepository;

class PageNewsRepository extends EntityRepository
{
    public function deleteByNews(News $news): int
    {
        $qb    = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->delete($this->getEntityName(), 'pn')
            ->where('pn.news = :news')
            ->setParameter('news', $news->getId())
            ->getQuery();

        return $query->execute();
    }
}
