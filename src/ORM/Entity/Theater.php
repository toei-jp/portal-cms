<?php

namespace App\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\ORM\Entity\AbstractEntity;

/**
 * Theater entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterRepository")
 * @ORM\Table(name="theater", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Theater extends AbstractEntity implements
    CampaignPublicationInterface,
    NewsPublicationInterface,
    MainBannerPublicationInterface
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    public const MASTER_VERSION_V1 = 1;
    public const MASTER_VERSION_V2 = 2;

    /**
     * id
     *
     * @ORM\Id
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @var int
     */
    protected $id;

    /**
     * name
     *
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $name;

    /**
     * name_ja
     *
     * @ORM\Column(type="string", name="name_ja")
     *
     * @var string
     */
    protected $nameJa;

    /**
     * area
     *
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     *
     * @var int
     */
    protected $area;

    /**
     * master_code
     *
     * @ORM\Column(type="string", name="master_code", length=3, options={"fixed":true})
     *
     * @var string
     */
    protected $masterCode;

    /**
     * display_order
     *
     * @ORM\Column(type="smallint", name="display_order", options={"unsigned"=true})
     *
     * @var int
     */
    protected $displayOrder;

    /**
     * meta
     *
     * 設計の問題でnullを許容する形になってしまったが、nullにならないようデータで調整する。
     *
     * @ORM\OneToOne(targetEntity="TheaterMeta", mappedBy="theater")
     *
     * @var TheaterMeta|null
     */
    protected $meta;

    /**
     * admin_users
     *
     * @ORM\OneToMany(targetEntity="AdminUser", mappedBy="theater")
     *
     * @var Collection<AdminUser>
     */
    protected $adminUsers;

    /**
     * campaigns
     *
     * @ORM\OneToMany(targetEntity="TheaterCampaign", mappedBy="theater", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     *
     * @var Collection<TheaterCampaign>
     */
    protected $campaigns;

    /**
     * news_list
     *
     * @ORM\OneToMany(targetEntity="TheaterNews", mappedBy="theater", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     *
     * @var Collection<TheaterNews>
     */
    protected $newsList;

    /**
     * main_banners
     *
     * @ORM\OneToMany(targetEntity="TheaterMainBanner", mappedBy="theater", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     *
     * @var Collection<TheaterMainBanner>
     */
    protected $mainBanners;

    /**
     * construct
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id          = $id;
        $this->adminUsers  = new ArrayCollection();
        $this->campaigns   = new ArrayCollection();
        $this->newsList    =  new ArrayCollection();
        $this->mainBanners = new ArrayCollection();
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
     * get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * get name_ja
     *
     * @return string
     */
    public function getNameJa()
    {
        return $this->nameJa;
    }

    /**
     * set name_ja
     *
     * @param string $nameJa
     * @return void
     */
    public function setNameJa(string $nameJa)
    {
        $this->nameJa = $nameJa;
    }

    /**
     * get area
     *
     * @return int
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * set area
     *
     * @param int $area
     * @return void
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * get master_code
     *
     * @return string
     */
    public function getMasterCode()
    {
        return $this->masterCode;
    }

    /**
     * set master_code
     *
     * @param string $masterCode
     * @return void
     */
    public function setMasterCode($masterCode)
    {
        $this->masterCode = $masterCode;
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

    /**
     * get meta
     *
     * @return TheaterMeta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * get admin_users
     *
     * @return Collection
     */
    public function getAdminUsers()
    {
        return $this->adminUsers;
    }

    /**
     * get campaigns
     *
     * @return Collection
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    /**
     * get news_list
     *
     * @return Collection
     */
    public function getNewsList(): Collection
    {
        return $this->newsList;
    }

    /**
     * get main_banners
     *
     * @return Collection
     */
    public function getMainBanners(): Collection
    {
        return $this->mainBanners;
    }
}
