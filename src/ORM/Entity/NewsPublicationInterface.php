<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\Common\Collections\Collection;

interface NewsPublicationInterface
{
    public function getNewsList(): Collection;
}
