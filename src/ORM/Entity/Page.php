<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Page entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\PageRepository")
 * @ORM\Table(name="page", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Page extends AbstractEntity implements
    CampaignPublicationInterface,
    NewsPublicationInterface,
    MainBannerPublicationInterface
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="name_ja")
     *
     * @var string
     */
    protected $nameJa;

    /**
     * @ORM\OneToMany(targetEntity="PageCampaign", mappedBy="page", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     *
     * @var Collection<PageCampaign>
     */
    protected $campaigns;

    /**
     * @ORM\OneToMany(targetEntity="PageNews", mappedBy="page", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     *
     * @var Collection<PageNews>
     */
    protected $newsList;

    /**
     * @ORM\OneToMany(targetEntity="PageMainBanner", mappedBy="page", orphanRemoval=true)
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     *
     * @var Collection<PageMainBanner>
     */
    protected $mainBanners;

    public function __construct(int $id)
    {
        $this->id          = $id;
        $this->campaigns   = new ArrayCollection();
        $this->newsList    =  new ArrayCollection();
        $this->mainBanners = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNameJa(): string
    {
        return $this->nameJa;
    }

    public function setNameJa(string $nameJa): void
    {
        $this->nameJa = $nameJa;
    }

    /**
     * @return Collection<PageCampaign>
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    /**
     * @return Collection<PageNews>
     */
    public function getNewsList(): Collection
    {
        return $this->newsList;
    }

    /**
     * @return Collection<PageMainBanner>
     */
    public function getMainBanners(): Collection
    {
        return $this->mainBanners;
    }
}
