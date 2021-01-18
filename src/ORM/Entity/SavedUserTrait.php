<?php

namespace App\ORM\Entity;

/**
 * SavedUser trait
 */
trait SavedUserTrait
{
    /**
     * created_user
     *
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumn(name="created_user_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var AdminUser|null
     */
    protected $createdUser;

    /**
     * updated_user
     *
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumn(name="updated_user_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var AdminUser|null
     */
    protected $updatedUser;

    /**
     * get created_user
     *
     * @return AdminUser|null
     */
    public function getCreatedUser()
    {
        return $this->createdUser;
    }

    /**
     * set created_user
     *
     * @param AdminUser|null $createdUser
     * @return void
     */
    public function setCreatedUser(?AdminUser $createdUser)
    {
        $this->createdUser = $createdUser;
    }

    /**
     * get updated_user
     *
     * @return AdminUser|null
     */
    public function getUpdatedUser()
    {
        return $this->updatedUser;
    }

    /**
     * set updated_user
     *
     * @param AdminUser|null $updatedUser
     * @return void
     */
    public function setUpdatedUser(?AdminUser $updatedUser)
    {
        $this->updatedUser = $updatedUser;
    }
}
