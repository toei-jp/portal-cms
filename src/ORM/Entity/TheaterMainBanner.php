<?php

/**
 * TheaterMainBanner.php
 */

namespace Toei\PortalAdmin\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Toei\PortalAdmin\ORM\Entity\AbstractEntity;

/**
 * TheaterMainBanner entity class
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Toei\PortalAdmin\ORM\Repository\TheaterMainBannerRepository")
 * @ORM\Table(name="theater_main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterMainBanner extends AbstractEntity
{
    use TimestampableTrait;

    /**
     * id
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * main_banner
     *
     * @ORM\ManyToOne(targetEntity="MainBanner")
     * @ORM\JoinColumn(name="main_banner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var MainBanner
     */
    protected $mainBanner;

    /**
     * theater
     *
     * @ORM\ManyToOne(targetEntity="Theater", inversedBy="theaters")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Theater
     */
    protected $theater;

    /**
     * display_order
     *
     * @ORM\Column(type="smallint", name="display_order", options={"unsigned"=true})
     *
     * @var int
     */
    protected $displayOrder;

    /**
     * construct
     */
    public function __construct()
    {
    }

    /**
     * get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get main_banner
     *
     * @return MainBanner
     */
    public function getMainBanner()
    {
        return $this->mainBanner;
    }

    /**
     * set main_banner
     *
     * @param MainBanner $mainBanner
     * @return void
     */
    public function setMainBanner(MainBanner $mainBanner)
    {
        $this->mainBanner = $mainBanner;
    }

    /**
     * get theater
     *
     * @return Theater
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * set theater
     *
     * @param Theater $theater
     * @return void
     */
    public function setTheater(Theater $theater)
    {
        $this->theater = $theater;
    }

    /**
     * get display_order
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * set display_order
     *
     * @param int $displayOrder
     * @return void
     */
    public function setDisplayOrder(int $displayOrder)
    {
        $this->displayOrder = $displayOrder;
    }
}
