<?php
/**
 * AdminUserRepository.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Toei\PortalAdmin\ORM\Entity\AdminUser;
use Toei\PortalAdmin\Pagination\DoctrinePaginator;

/**
 * AdminUser repository class
 */
class AdminUserRepository extends EntityRepository
{
    /**
     * find for list page
     * 
     * @param array $params
     * @param int   $page
     * @param int   $maxPerPage
     * @return DoctrinePaginator
     */
    public function findForList(array $params, int $page, int $maxPerPage = 10)
    {
        $qb = $this->createQueryBuilder('au');
        $qb
            ->where('au.isDeleted = false')
            ->orderBy('au.id', 'ASC');
        
        $query = $qb->getQuery();
        
        return new DoctrinePaginator($query, $page, $maxPerPage);
    }
    
    /**
     * find one by id
     *
     * @param int $id
     * @return AdminUser|null
     */
    public function findOneById($id)
    {
        $qb = $this->createQueryBuilder('au');
        $qb
            ->where('au.id = :id')
            ->andWhere('au.isDeleted = false')
            ->setParameter('id', $id);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    /**
     * find one by name
     *
     * @param string $name
     * @return AdminUser|null
     */
    public function findOneByName($name)
    {
        $qb = $this->createQueryBuilder('au');
        $qb
            ->where('au.name = :name')
            ->andWhere('au.isDeleted = false')
            ->setParameter('name', $name);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
}