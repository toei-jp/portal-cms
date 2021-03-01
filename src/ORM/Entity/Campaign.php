<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\CampaignRepository")
 * @ORM\Table(name="campaign", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Campaign extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var Title|null
     */
    protected $title;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     *
     * @var File
     */
    protected $image;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime", name="start_dt")
     *
     * @var DateTime
     */
    protected $startDt;

    /**
     * @ORM\Column(type="datetime", name="end_dt")
     *
     * @var DateTime
     */
    protected $endDt;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity="PageCampaign", mappedBy="campaign")
     *
     * @var Collection<PageCampaign>
     */
    protected $pages;

    /**
     * @ORM\OneToMany(targetEntity="TheaterCampaign", mappedBy="campaign")
     *
     * @var Collection<TheaterCampaign>
     */
    protected $theaters;

    public function __construct()
    {
        $this->pages    = new ArrayCollection();
        $this->theaters = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    public function setTitle(?Title $title): void
    {
        $this->title = $title;
    }

    public function getImage(): File
    {
        return $this->image;
    }

    public function setImage(File $image): void
    {
        $this->image = $image;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStartDt(): DateTime
    {
        return $this->startDt;
    }

    /**
     * @param DateTime|string $startDt
     */
    public function setStartDt($startDt): void
    {
        if ($startDt instanceof DateTime) {
            $this->startDt = $startDt;
        } else {
            $this->startDt = new DateTime($startDt);
        }
    }

    public function getEndDt(): DateTime
    {
        return $this->endDt;
    }

    /**
     * @param DateTime|string $endDt
     */
    public function setEndDt($endDt): void
    {
        if ($endDt instanceof DateTime) {
            $this->endDt = $endDt;
        } else {
            $this->endDt = new DateTime($endDt);
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return Collection<PageCampaign>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * @return Collection<TheaterCampaign>
     */
    public function getTheaters(): Collection
    {
        return $this->theaters;
    }

    public function getPublishedTargets(): ArrayCollection
    {
        $publications = new ArrayCollection();

        foreach ($this->getPages() as $pageCampaign) {
            /** @var PageCampaign $pageCampaign */
            $publications->add($pageCampaign->getPage());
        }

        foreach ($this->getTheaters() as $theaterCampaign) {
            /** @var TheaterCampaign $theaterCampaign */
            $publications->add($theaterCampaign->getTheater());
        }

        return $publications;
    }
}
