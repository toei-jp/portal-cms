<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdminUser entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\AdminUserRepository")
 * @ORM\Table(name="admin_user", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdminUser extends AbstractEntity
{
    use SoftDeleteTrait;
    use TimestampableTrait;

    public const GROUP_MASTER  = 1;
    public const GROUP_MANAGER = 2;
    public const GROUP_THEATER = 3;

    /** @var array<int, string> */
    protected static $groups = [
        self::GROUP_MASTER  => 'マスター',
        self::GROUP_MANAGER => 'マネージャー',
        self::GROUP_THEATER => '劇場',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="display_name")
     *
     * @var string
     */
    protected $displayName;

    /**
     * @ORM\Column(type="string", length=60, options={"fixed":true})
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="smallint", name="`group`", options={"unsigned"=true})
     *
     * @var int
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="Theater", inversedBy="adminUsers")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var Theater|null
     */
    protected $theater;

    /**
     * @return array<int, string>
     */
    public static function getGroups(): array
    {
        return self::$groups;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function getGroup(): int
    {
        return $this->group;
    }

    public function getGroupLabel(): ?string
    {
        return self::$groups[$this->getGroup()] ?? null;
    }

    public function setGroup(int $group): void
    {
        $this->group = $group;
    }

    public function isGroup(int $group): bool
    {
        return $this->getGroup() === $group;
    }

    public function isMaster(): bool
    {
        return $this->isGroup(self::GROUP_MASTER);
    }

    public function isManager(): bool
    {
        return $this->isGroup(self::GROUP_MANAGER);
    }

    public function isTheater(): bool
    {
        return $this->isGroup(self::GROUP_THEATER);
    }

    public function getTheater(): ?Theater
    {
        return $this->theater;
    }

    public function setTheater(?Theater $theater): void
    {
        $this->theater = $theater;
    }
}
