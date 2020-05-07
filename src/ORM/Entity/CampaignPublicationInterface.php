<?php

/**
 * CampaignPublicationInterface.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * CampaignPublication interface
 */
interface CampaignPublicationInterface
{
    /**
     * get campaigns
     *
     * @return Collection
     */
    public function getCampaigns(): Collection;
}
