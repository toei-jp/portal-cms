<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MainBanner entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\MainBannerRepository")
 * @ORM\Table(name="main_banner", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class MainBanner extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    public const LINK_TYPE_NONE = 1;
    public const LINK_TYPE_URL  = 2;

    /** @var array<int, string> */
    protected static array $linkTypes = [
        self::LINK_TYPE_NONE => 'リンクなし',
        self::LINK_TYPE_URL  => 'URL',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\OneToOne(targetEntity="File")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected File $image;

    /** @ORM\Column(type="string") */
    protected string $name;

    /** @ORM\Column(type="smallint", name="link_type", options={"unsigned"=true}) */
    protected int $linkType;

    /** @ORM\Column(type="string", name="link_url", nullable=true) */
    protected ?string $linkUrl = null;

    /**
     * @ORM\OneToMany(targetEntity="PageMainBanner", mappedBy="mainBanner")
     *
     * @var Collection<PageMainBanner>
     */
    protected Collection $pages;

    /**
     * @ORM\OneToMany(targetEntity="TheaterMainBanner", mappedBy="mainBanner")
     *
     * @var Collection<TheaterMainBanner>
     */
    protected Collection $theaters;

    /**
     * @return array<int, string>
     */
    public static function getLinkTypes(): array
    {
        return self::$linkTypes;
    }

    public function __construct()
    {
        $this->pages    = new ArrayCollection();
        $this->theaters = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getLinkType(): int
    {
        return $this->linkType;
    }

    public function setLinkType(int $linkType): void
    {
        $this->linkType = $linkType;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(?string $linkUrl): void
    {
        $this->linkUrl = $linkUrl;
    }

    /**
     * @return Collection<PageMainBanner>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * @return Collection<TheaterMainBanner>
     */
    public function getTheaters(): Collection
    {
        return $this->theaters;
    }
}
