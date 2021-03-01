<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TheaterMeta entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TheaterMetaRepository")
 * @ORM\Table(name="theater_meta", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class TheaterMeta extends AbstractEntity
{
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
     * @ORM\OneToOne(targetEntity="Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Theater
     */
    protected $theater;

    /**
     * @ORM\Column(type="json", name="opening_hours")
     *
     * @var array{type:int,from_date:string,to_date:string|null,time:string}[]
     */
    protected $openingHours;

    public function __construct()
    {
        $this->openingHours = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTheater(): Theater
    {
        return $this->theater;
    }

    public function setTheater(Theater $theater): void
    {
        $this->theater = $theater;
    }

    /**
     * @return TheaterOpeningHour[]
     */
    public function getOpeningHours(): array
    {
        $hours = [];

        if (is_array($this->openingHours)) {
            foreach ($this->openingHours as $hour) {
                $hours[] = TheaterOpeningHour::create($hour);
            }
        }

        return $hours;
    }

    /**
     * @param TheaterOpeningHour[] $openingHours
     */
    public function setOpeningHours(array $openingHours): void
    {
        $hours = [];

        foreach ($openingHours as $hour) {
            /** @var TheaterOpeningHour $hour */
            $hours[] = $hour->toArray();
        }

        $this->openingHours = $hours;
    }
}
