<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;

trait TimestampableTrait
{
    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     *
     * @var DateTime
     */
    protected $updatedAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|string $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt instanceof DateTime
            ? $createdAt
            : new DateTime($createdAt);
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|string $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt instanceof DateTime
            ? $updatedAt
            : new DateTime($updatedAt);
    }

    /**
     * @ORM\PrePersist
     */
    public function persistTimestamp(): void
    {
        $this->setCreatedAt('now');
        $this->setUpdatedAt('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateTimestamp(): void
    {
        $this->setUpdatedAt('now');
    }
}
