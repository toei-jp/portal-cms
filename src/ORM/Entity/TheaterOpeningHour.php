<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;

/**
 * TheaterOpeningHour entity class
 *
 * 現時点ではDoctrineのEntityとはしない。
 * DBへはjsonデータへ変換して文字列カラムへセットする。
 */
class TheaterOpeningHour extends AbstractEntity
{
    public const TYPE_DATE = 1;
    public const TYPE_TERM = 2;

    protected int $type;

    protected DateTime $fromDate;

    protected ?DateTime $toDate = null;

    protected DateTime $time;

    /**
     * @param array<string, mixed> $array
     */
    public static function create(array $array): self
    {
        $entity = new self();
        $entity->setType((int) $array['type']);
        $entity->setFromDate($array['from_date']);
        $entity->setToDate($array['to_date']);
        $entity->setTime($array['time']);

        return $entity;
    }

    public function __construct()
    {
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getFromDate(): DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param DateTime|string $fromDate
     */
    public function setFromDate($fromDate): void
    {
        if ($fromDate instanceof DateTime) {
            $this->fromDate = $fromDate;
        } else {
            $this->fromDate = new DateTime($fromDate);
        }
    }

    public function getToDate(): ?DateTime
    {
        return $this->toDate;
    }

    /**
     * @param DateTime|string|null $toDate
     */
    public function setToDate($toDate): void
    {
        if (is_null($toDate) || $toDate instanceof DateTime) {
            $this->toDate = $toDate;
        } else {
            $this->toDate = new DateTime($toDate);
        }
    }

    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime|string $time
     */
    public function setTime($time): void
    {
        if ($time instanceof DateTime) {
            $this->time = $time;
        } else {
            $this->time = new DateTime($time);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = [];

        $array['type']      = $this->type;
        $array['from_date'] = $this->fromDate->format('Y/m/d');
        $array['to_date']   = is_null($this->toDate)
            ? null
            : $this->toDate->format('Y/m/d');
        $array['time']      = $this->time->format('H:i:s');

        return $array;
    }
}
