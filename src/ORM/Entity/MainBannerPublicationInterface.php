<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\Common\Collections\Collection;

interface MainBannerPublicationInterface
{
    public function getMainBanners(): Collection;
}
