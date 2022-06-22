<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Title entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\TitleRepository")
 * @ORM\Table(name="title", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class Title extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    public const RATING_G    = 1;
    public const RATING_PG12 = 2;
    public const RATING_R15  = 3;
    public const RATING_R18  = 4;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    protected ?File $image = null;

    /** @ORM\Column(name="chever_code", type="string", length=100, unique=true, nullable=true) */
    protected ?string $cheverCode = null;

    /** @ORM\Column(type="string") */
    protected string $name;

    /** @ORM\Column(type="string", name="name_kana", nullable=true) */
    protected ?string $nameKana = null;

    /** @ORM\Column(type="string", name="sub_title", nullable=true) */
    protected ?string $subTitle = null;

    /** @ORM\Column(type="string", nullable=true) */
    protected ?string $credit = null;

    /** @ORM\Column(type="text", nullable=true) */
    protected ?string $catchcopy = null;

    /** @ORM\Column(type="text", nullable=true) */
    protected ?string $introduction = null;

    /** @ORM\Column(type="string", nullable=true) */
    protected ?string $director = null;

    /** @ORM\Column(type="string", nullable=true) */
    protected ?string $cast = null;

    /** @ORM\Column(type="date", name="publishing_expected_date", nullable=true) */
    protected ?DateTime $publishingExpectedDate = null;

    /** @ORM\Column(type="string", name="official_site", nullable=true) */
    protected ?string $officialSite = null;

    /** @ORM\Column(type="smallint", nullable=true, options={"unsigned"=true}) */
    protected ?int $rating = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     *
     * @var array<int>|null
     */
    protected ?array $universal = null;

    /** @var array<int, string> */
    protected static array $ratingTypes = [
        self::RATING_G    => 'G',
        self::RATING_PG12 => 'PG12',
        self::RATING_R15  => 'R15+',
        self::RATING_R18  => 'R18+',
    ];

    /** @var array<int, string> */
    protected static array $universalTypes = [
        '1' => '音声上映',
        '2' => '字幕上映',
    ];

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getImage(): ?File
    {
        return $this->image;
    }

    public function setImage(?File $image): void
    {
        $this->image = $image;
    }

    public function getCheverCode(): ?string
    {
        return $this->cheverCode;
    }

    public function setCheverCode(?string $cheverCode): void
    {
        $this->cheverCode = $cheverCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNameKana(): ?string
    {
        return $this->nameKana;
    }

    public function setNameKana(?string $nameKana): void
    {
        $this->nameKana = $nameKana;
    }

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(?string $subTitle): void
    {
        $this->subTitle = $subTitle;
    }

    public function getCredit(): ?string
    {
        return $this->credit;
    }

    public function setCredit(?string $credit): void
    {
        $this->credit = $credit;
    }

    public function getCatchcopy(): ?string
    {
        return $this->catchcopy;
    }

    public function setCatchcopy(?string $catchcopy): void
    {
        $this->catchcopy = $catchcopy;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): void
    {
        $this->introduction = $introduction;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(?string $director): void
    {
        $this->director = $director;
    }

    public function getCast(): ?string
    {
        return $this->cast;
    }

    public function setCast(?string $cast): void
    {
        $this->cast = $cast;
    }

    public function getPublishingExpectedDate(): ?DateTime
    {
        return $this->publishingExpectedDate;
    }

    /**
     * @param DateTime|string|null $publishingExpectedDate
     */
    public function setPublishingExpectedDate($publishingExpectedDate): void
    {
        if (is_null($publishingExpectedDate) || ($publishingExpectedDate instanceof DateTime)) {
            $this->publishingExpectedDate = $publishingExpectedDate;
        } else {
            $this->publishingExpectedDate = new DateTime($publishingExpectedDate);
        }
    }

    public function getOfficialSite(): ?string
    {
        return $this->officialSite;
    }

    public function setOfficialSite(?string $officialSite): void
    {
        $this->officialSite = $officialSite;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return array<int>|null
     */
    public function getUniversal(): ?array
    {
        return $this->universal;
    }

    /**
     * @return string[]
     */
    public function getUniversalLabel(): array
    {
        $univarsal = $this->getUniversal();
        $types     = self::getUniversalTypes();
        $labels    = [];

        foreach ($univarsal as $value) {
            if (isset($types[$value])) {
                $labels[] = $types[$value];
            }
        }

        return $labels;
    }

    /**
     * @param array<int>|null $universal
     */
    public function setUniversal(?array $universal): void
    {
        $this->universal = $universal;
    }

    /**
     * @return array<int, string>
     */
    public static function getRatingTypes(): array
    {
        return self::$ratingTypes;
    }

    /**
     * @return array<int, string>
     */
    public static function getUniversalTypes(): array
    {
        return self::$universalTypes;
    }
}
