<?php

/**
 * NewsPublicationInterface.php
 */

namespace Toei\PortalAdmin\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * NewsPublication interface
 */
interface NewsPublicationInterface
{
    /**
     * get newsList
     *
     * @return Collection
     */
    public function getNewsList(): Collection;
}
