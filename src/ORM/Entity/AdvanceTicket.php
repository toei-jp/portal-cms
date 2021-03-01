<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdvanceTicket entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\AdvanceTicketRepository")
 * @ORM\Table(name="advance_ticket", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdvanceTicket extends AbstractEntity
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    public const TYPE_MVTK  = 1;
    public const TYPE_PAPER = 2;

    public const SPECIAL_GIFT_STOCK_IN     = 1;
    public const SPECIAL_GIFT_STOCK_FEW    = 2;
    public const SPECIAL_GIFT_STOCK_NOT_IN = 3;

    public const STATUS_PRE_SALE = 1;
    public const STATUS_SALE     = 2;
    public const STATUS_SALE_END = 3;

    /** @var array<int, string> */
    protected static $types = [
        self::TYPE_MVTK  => 'ムビチケ',
        self::TYPE_PAPER => '紙券',
    ];

    /** @var array<int, string> */
    protected static $specialGiftStockList = [
        self::SPECIAL_GIFT_STOCK_IN     => '有り',
        self::SPECIAL_GIFT_STOCK_FEW    => '残り僅か',
        self::SPECIAL_GIFT_STOCK_NOT_IN => '特典終了',
    ];

    /** @var array<int, string> */
    protected static $statusList = [
        self::STATUS_PRE_SALE => '販売予定',
        self::STATUS_SALE     => '販売中',
        self::STATUS_SALE_END => '販売終了',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AdvanceSale")
     * @ORM\JoinColumn(name="advance_sale_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     *
     * @var AdvanceSale
     */
    protected $advanceSale;

    /**
     * @ORM\Column(type="datetime", name="release_dt")
     *
     * @var DateTime
     */
    protected $releaseDt;

    /**
     * @ORM\Column(type="string", name="release_dt_text", nullable=true)
     *
     * @var string|null
     */
    protected $releaseDtText;

    /**
     * @ORM\Column(type="boolean", name="is_sales_end", options={"default":false})
     *
     * @var bool
     */
    protected $isSalesEnd;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     *
     * @var int
     */
    protected $type;

    /**
     * @ORM\Column(type="string", name="price_text", nullable=true)
     *
     * @var string|null
     */
    protected $priceText;

    /**
     * @ORM\Column(type="string", name="special_gift", nullable=true)
     *
     * @var string|null
     */
    protected $specialGift;

    /**
     * @ORM\Column(type="smallint", name="special_gift_stock", nullable=true, options={"unsigned"=true})
     *
     * @var int|null
     */
    protected $specialGiftStock;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="special_gift_image", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var File|null
     */
    protected $specialGiftImage;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAdvanceSale(): AdvanceSale
    {
        return $this->advanceSale;
    }

    public function setAdvanceSale(AdvanceSale $advanceSale): void
    {
        $this->advanceSale = $advanceSale;
    }

    public function getReleaseDt(): DateTime
    {
        return $this->releaseDt;
    }

    /**
     * @param DateTime|string $releaseDt
     */
    public function setReleaseDt($releaseDt): void
    {
        if ($releaseDt instanceof DateTime) {
            $this->releaseDt = $releaseDt;
        } else {
            $this->releaseDt = new DateTime($releaseDt);
        }
    }

    public function getReleaseDtText(): ?string
    {
        return $this->releaseDtText;
    }

    public function setReleaseDtText(?string $releaseDtText): void
    {
        $this->releaseDtText = $releaseDtText;
    }

    public function getIsSalesEnd(): bool
    {
        return $this->isSalesEnd;
    }

    /**
     * alias getIsSalesEnd()
     */
    public function isSalseEnd(): bool
    {
        return $this->getIsSalesEnd();
    }

    public function setIsSalesEnd(bool $isSalesEnd): void
    {
        $this->isSalesEnd = $isSalesEnd;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getTypeLabel(): ?string
    {
        return self::$types[$this->getType()] ?? null;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getPriceText(): ?string
    {
        return $this->priceText;
    }

    public function setPriceText(?string $priceText): void
    {
        $this->priceText = $priceText;
    }

    public function getSpecialGift(): ?string
    {
        return $this->specialGift;
    }

    public function setSpecialGift(?string $specialGift): void
    {
        $this->specialGift = $specialGift;
    }

    public function getSpecialGiftStock(): ?int
    {
        return $this->specialGiftStock;
    }

    public function getSpecialGiftStockLabel(): ?string
    {
        return self::$specialGiftStockList[$this->getSpecialGiftStock()] ?? null;
    }

    public function setSpecialGiftStock(?int $specialGiftStock): void
    {
        $this->specialGiftStock = $specialGiftStock ?: null;
    }

    public function getSpecialGiftImage(): ?File
    {
        return $this->specialGiftImage;
    }

    public function setSpecialGiftImage(?File $specialGiftImage): void
    {
        $this->specialGiftImage = $specialGiftImage;
    }

    public function getStatusLabel(): ?string
    {
        if ($this->isSalseEnd()) {
            return self::$statusList[self::STATUS_SALE_END];
        }

        $now = new DateTime('now');
        $end = $this->getAdvanceSale()->getPublishingExpectedDate();

        if ($end && $now > $end) {
            return self::$statusList[self::STATUS_SALE_END];
        }

        $start = $this->getReleaseDt();

        if ($now < $start) {
            return self::$statusList[self::STATUS_PRE_SALE];
        }

        // 終了日（作品公開予定日）が設定されていなくても発売される
        return self::$statusList[self::STATUS_SALE];
    }

    /**
     * @return array<int, string>
     */
    public static function getTypes(): array
    {
        return self::$types;
    }

    /**
     * @return array<int, string>
     */
    public static function getSpecialGiftStockList(): array
    {
        return self::$specialGiftStockList;
    }
}
