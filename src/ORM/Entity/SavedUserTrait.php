<?php

declare(strict_types=1);

namespace App\ORM\Entity;

trait SavedUserTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumn(name="created_user_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var AdminUser|null
     */
    protected $createdUser;

    /**
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumn(name="updated_user_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     *
     * @var AdminUser|null
     */
    protected $updatedUser;

    public function getCreatedUser(): ?AdminUser
    {
        return $this->createdUser;
    }

    public function setCreatedUser(?AdminUser $createdUser): void
    {
        $this->createdUser = $createdUser;
    }

    public function getUpdatedUser(): ?AdminUser
    {
        return $this->updatedUser;
    }

    public function setUpdatedUser(?AdminUser $updatedUser): void
    {
        $this->updatedUser = $updatedUser;
    }
}
