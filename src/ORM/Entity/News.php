<?php

namespace App\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\ORM\Entity\AbstractEntity;

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

    /** @var array */
    public static $categories = [self::CATEGORY_TOPICS => 'トピックス'];

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
     * title
     *
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var Title|null
     */
    protected $title;

    /**
     * image
     *
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     *
     * @var File
     */
    protected $image;

    /**
     * category
     *
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     *
     * @var int
     */
    protected $category;

    /**
     * headline
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $headline;

    /**
     * body
     *
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $body;

    /**
     * start_dt
     *
     * @ORM\Column(type="datetime", name="start_dt")
     *
     * @var \DateTime
     */
    protected $startDt;

    /**
     * end_dt
     *
     * @ORM\Column(type="datetime", name="end_dt")
     *
     * @var \DateTime
     */
    protected $endDt;

    /**
     * pages
     *
     * @ORM\OneToMany(targetEntity="PageNews", mappedBy="news")
     *
     * @var Collection<PageNews>
     */
    protected $pages;

    /**
     * theaters
     *
     * @ORM\OneToMany(targetEntity="TheaterNews", mappedBy="news")
     *
     * @var Collection<TheaterNews>
     */
    protected $theaters;

    /**
     * construct
     */
    public function __construct()
    {
        $this->pages    = new ArrayCollection();
        $this->theaters = new ArrayCollection();
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
     * get title
     *
     * @return Title|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set title
     *
     * @param Title|null $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * get image
     *
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * set image
     *
     * @param File $image
     * @return void
     */
    public function setImage(File $image)
    {
        $this->image = $image;
    }

    /**
     * get category
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * get category label
     *
     * @return string|null
     */
    public function getCategoryLabel()
    {
        return self::$categories[$this->getCategory()] ?? null;
    }

    /**
     * set category
     *
     * @param int $category
     * @return void
     */
    public function setCategory(int $category)
    {
        $this->category = $category;
    }

    /**
     * get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * set headline
     *
     * @param string $headline
     * @return void
     */
    public function setHeadline(string $headline)
    {
        $this->headline = $headline;
    }

    /**
     * get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * set body
     *
     * @param string $body
     * @return void
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * get start_dt
     *
     * @return \DateTime
     */
    public function getStartDt()
    {
        return $this->startDt;
    }

    /**
     * set start_dt
     *
     * @param \DateTime|string $startDt
     * @return void
     */
    public function setStartDt($startDt)
    {
        if ($startDt instanceof \DateTime) {
            $this->startDt = $startDt;
        } else {
            $this->startDt = new \DateTime($startDt);
        }
    }

    /**
     * get end_dt
     *
     * @return \DateTime
     */
    public function getEndDt()
    {
        return $this->endDt;
    }

    /**
     * set end_dt
     *
     * @param \DateTime|string $endDt
     * @return void
     */
    public function setEndDt($endDt)
    {
        if ($endDt instanceof \DateTime) {
            $this->endDt = $endDt;
        } else {
            $this->endDt = new \DateTime($endDt);
        }
    }

    /**
     * get pages
     *
     * @return Collection
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * get theaters
     *
     * @return Collection
     */
    public function getTheaters(): Collection
    {
        return $this->theaters;
    }

    /**
     * get published target
     *
     * @return ArrayCollection
     */
    public function getPublishedTargets()
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
