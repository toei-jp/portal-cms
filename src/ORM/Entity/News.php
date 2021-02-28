<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * News entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\NewsRepository")
 * @ORM\Table(name="news", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class News extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    public const CATEGORY_TOPICS = 1;

    /** @var array<int, string> */
    public static $categories = [self::CATEGORY_TOPICS => 'トピックス'];

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
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     *
     * @var int
     */
    protected $category;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $headline;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $body;

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
     * @ORM\OneToMany(targetEntity="PageNews", mappedBy="news")
     *
     * @var Collection<PageNews>
     */
    protected $pages;

    /**
     * @ORM\OneToMany(targetEntity="TheaterNews", mappedBy="news")
     *
     * @var Collection<TheaterNews>
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

    public function getCategory(): int
    {
        return $this->category;
    }

    public function getCategoryLabel(): ?string
    {
        return self::$categories[$this->getCategory()] ?? null;
    }

    public function setCategory(int $category): void
    {
        $this->category = $category;
    }

    public function getHeadline(): string
    {
        return $this->headline;
    }

    public function setHeadline(string $headline): void
    {
        $this->headline = $headline;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
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

    /**
     * @return Collection<PageNews>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * @return Collection<TheaterNews>
     */
    public function getTheaters(): Collection
    {
        return $this->theaters;
    }

    public function getPublishedTargets(): ArrayCollection
    {
        $publications = new ArrayCollection();

        foreach ($this->getPages() as $pageNews) {
            /** @var PageNews $pageNews */
            $publications->add($pageNews->getPage());
        }

        foreach ($this->getTheaters() as $theaterNews) {
            /** @var TheaterNews $theaterNews */
            $publications->add($theaterNews->getTheater());
        }

        return $publications;
    }
}
