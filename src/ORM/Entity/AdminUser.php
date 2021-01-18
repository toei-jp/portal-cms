<?php

namespace App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ORM\Entity\AbstractEntity;

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

    /** @var array */
    protected static $groups = [
        self::GROUP_MASTER  => 'マスター',
        self::GROUP_MANAGER => 'マネージャー',
        self::GROUP_THEATER => '劇場',
    ];

    /**
     * id
     *
     * @ORM\Id
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * name
     *
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    protected $name;

    /**
     * display_name
     *
     * @ORM\Column(type="string", name="display_name")
     *
     * @var string
     */
    protected $displayName;

    /**
     * password
     *
     * @ORM\Column(type="string", length=60, options={"fixed":true})
     *
     * @var string
     */
    protected $password;

    /**
     * group
     *
     * @ORM\Column(type="smallint", name="`group`", options={"unsigned"=true})
     *
     * @var int
     */
    protected $group;

    /**
     * theater
     *
     * @ORM\ManyToOne(targetEntity="Theater", inversedBy="adminUsers")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var Theater|null
     */
    protected $theater;

    /**
     * return groups
     *
     * @return array
     */
    public static function getGroups()
    {
        return self::$groups;
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
     * get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * get display_name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * set display_name
     *
     * @param string $displayName
     * @return void
     */
    public function setDisplayName(string $displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * set password
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * get group
     *
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * get group label
     *
     * @return string|null
     */
    public function getGroupLabel()
    {
        return self::$groups[$this->getGroup()] ?? null;
    }

    /**
     * set group
     *
     * @param int $group
     * @return void
     */
    public function setGroup(int $group)
    {
        $this->group = $group;
    }

    /**
     * is group
     *
     * @param int $group
     * @return boolean
     */
    public function isGroup(int $group)
    {
        return $this->getGroup() === $group;
    }

    /**
     * is master group
     *
     * @return boolean
     */
    public function isMaster()
    {
        return $this->isGroup(self::GROUP_MASTER);
    }

    /**
     * is manager group
     *
     * @return boolean
     */
    public function isManager()
    {
        return $this->isGroup(self::GROUP_MANAGER);
    }

    /**
     * is theater group
     *
     * @return boolean
     */
    public function isTheater()
    {
        return $this->isGroup(self::GROUP_THEATER);
    }

    /**
     * get theater
     *
     * @return Theater
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * set theater
     *
     * @param Theater $theater
     * @return void
     */
    public function setTheater(Theater $theater)
    {
        $this->theater = $theater;
    }
}
