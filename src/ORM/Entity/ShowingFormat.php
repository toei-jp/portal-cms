<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShowingFormat entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="showing_format", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class ShowingFormat extends AbstractEntity
{
    use TimestampableTrait;

    /** @var array<int, string> */
    protected static array $systemList = [
        1  => '2D',
        2  => '3D',
        3  => '4DX',
        4  => '4DX3D',
        5  => 'IMAX',
        6  => 'IMAX3D',
        7  => 'BESTIA',
        8  => 'BESTIA3D',
        9  => 'dts-X',
        10 => 'ScreenX', // SASAKI-351
        99 => 'なし',
    ];

    /** @var array<int, string> */
    protected static array $voiceList = [
        1 => '字幕',
        2 => '吹替',
        3 => 'なし', // SASAKI-297
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Schedule")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected Schedule $schedule;

    /** @ORM\Column(type="smallint", options={"unsigned"=true}) */
    protected int $system;

    /** @ORM\Column(type="smallint", options={"unsigned"=true}) */
    protected int $voice;

    /**
     * @return array<int, string>
     */
    public static function getSystemList(): array
    {
        return self::$systemList;
    }

    /**
     * @return array<int, string>
     */
    public static function getVoiceList(): array
    {
        return self::$voiceList;
    }

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(Schedule $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function getSystem(): int
    {
        return $this->system;
    }

    public function getSystemLabel(): ?string
    {
        return self::$systemList[$this->getSystem()] ?? null;
    }

    public function setSystem(int $system): void
    {
        $this->system = $system;
    }

    public function getVoice(): int
    {
        return $this->voice;
    }

    public function getVoiceLabel(): ?string
    {
        return self::$voiceList[$this->getVoice()] ?? null;
    }

    public function setVoice(int $voice): void
    {
        $this->voice = $voice;
    }
}
