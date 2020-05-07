<?php

/**
 * TheaterCampaignRepository.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Toei\PortalAdmin\ORM\Entity\Campaign;
use Toei\PortalAdmin\ORM\Entity\TheaterCampaign;

/**
 * TheaterCampaign repository class
 */
class TheaterCampaignRepository extends EntityRepository
{
    /**
     * delete by Campaign
     *
     * @param Campaign $campaign
     * @return int
     */
    public function deleteByCampaign(Campaign $campaign)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $query = $qb
            ->delete($this->getEntityName(), 'tc')
            ->where('tc.campaign = :campaign')
            ->setParameter('campaign', $campaign->getId())
            ->getQuery();
        
        return $query->execute();
    }
}
