<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\ScheduleRepository")
 * @ORM\Table(name="schedule", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Schedule extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected Title $title;

    /** @ORM\Column(type="date", name="start_date") */
    protected DateTime $startDate;

    /** @ORM\Column(type="date", name="end_date") */
    protected DateTime $endDate;

    /** @ORM\Column(type="datetime", name="public_start_dt") */
    protected DateTime $publicStartDt;

    /** @ORM\Column(type="datetime", name="public_end_dt") */
    protected DateTime $publicEndDt;

    /** @ORM\Column(type="text", nullable=true) */
    protected ?string $remark = null;

    /**
     * @ORM\OneToMany(targetEntity="ShowingFormat", mappedBy="schedule", orphanRemoval=true)
     *
     * @var Collection<ShowingFormat>
     */
    protected Collection $showingFormats;

    /**
     * @ORM\OneToMany(targetEntity="ShowingTheater", mappedBy="schedule", orphanRemoval=true)
     *
     * @var Collection<ShowingTheater>
     */
    protected Collection $showingTheaters;

    public function __construct()
    {
        $this->showingFormats  = new ArrayCollection();
        $this->showingTheaters = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(Title $title): void
    {
        $this->title = $title;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime|string $startDate
     */
    public function setStartDate($startDate): void
    {
        if ($startDate instanceof DateTime) {
            $this->startDate = $startDate;
        } else {
            $this->startDate = new DateTime($startDate);
        }
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|string $endDate
     */
    public function setEndDate($endDate): void
    {
        if ($endDate instanceof DateTime) {
            $this->endDate = $endDate;
        } else {
            $this->endDate = new DateTime($endDate);
        }
    }

    public function getPublicStartDt(): DateTime
    {
        return $this->publicStartDt;
    }

    /**
     * @param DateTime|string $publicStartDt
     */
    public function setPublicStartDt($publicStartDt): void
    {
        if ($publicStartDt instanceof DateTime) {
            $this->publicStartDt = $publicStartDt;
        } else {
            $this->publicStartDt = new DateTime($publicStartDt);
        }
    }

    public function getPublicEndDt(): DateTime
    {
        return $this->publicEndDt;
    }

    /**
     * @param DateTime|string $publicEndDt
     */
    public function setPublicEndDt($publicEndDt): void
    {
        if ($publicEndDt instanceof DateTime) {
            $this->publicEndDt = $publicEndDt;
        } else {
            $this->publicEndDt = new DateTime($publicEndDt);
        }
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return Collection<ShowingFormat>
     */
    public function getShowingFormats(): Collection
    {
        return $this->showingFormats;
    }

    /**
     * @param Collection<ShowingFormat> $showingFormats
     */
    public function setShowingFormats(Collection $showingFormats): void
    {
        $this->showingFormats = $showingFormats;
    }

    /**
     * @return Collection<ShowingTheater>
     */
    public function getShowingTheaters(): Collection
    {
        return $this->showingTheaters;
    }

    /**
     * @param Collection<ShowingTheater> $showingTheaters
     */
    public function setShowingTheaters(Collection $showingTheaters): void
    {
        $this->showingTheaters = $showingTheaters;
    }
}
