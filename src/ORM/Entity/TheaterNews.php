<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterNews entity class
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterNewsRepository")
 * @ORM\Table(name="theater_news", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterNews extends AbstractEntity
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="News")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected News $news;

    /**
     * @ORM\ManyToOne(targetEntity="Theater", inversedBy="newsList")
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

    public function getNews(): News
    {
        return $this->news;
    }

    public function setNews(News $news): void
    {
        $this->news = $news;
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
