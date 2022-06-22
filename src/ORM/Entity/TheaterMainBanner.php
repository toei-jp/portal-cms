<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterMainBanner entity class
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterMainBannerRepository")
 * @ORM\Table(name="theater_main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterMainBanner extends AbstractEntity
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="MainBanner")
     * @ORM\JoinColumn(name="main_banner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected MainBanner $mainBanner;

    /**
     * @ORM\ManyToOne(targetEntity="Theater", inversedBy="theaters")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Theater $theater;

    /** @ORM\Column(type="smallint", name="display_order", options={"unsigned"=true}) */
    protected int $displayOrder;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMainBanner(): MainBanner
    {
        return $this->mainBanner;
    }

    public function setMainBanner(MainBanner $mainBanner): void
    {
        $this->mainBanner = $mainBanner;
    }

    public function getTheater(): Theater
    {
        return $this->theater;
    }

    public function setTheater(Theater $theater): void
    {
        $this->theater = $theater;
    }

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): void
    {
        $this->displayOrder = $displayOrder;
    }
}
