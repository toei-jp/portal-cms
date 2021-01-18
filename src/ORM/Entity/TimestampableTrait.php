<?php

namespace App\ORM\Entity;

/**
 * Timestampable trait
 *
 * 作成日時、更新日時に関する機能。
 */
trait TimestampableTrait
{
    /**
     * crated_at
     *
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * updated_at
     *
     * @ORM\Column(type="datetime", name="updated_at")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * set created_at
     *
     * @param \DateTime|string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = ($createdAt instanceof \DateTime)
                        ? $createdAt
                        : new \DateTime($createdAt);
    }

    /**
     * get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * set updated_at
     *
     * @param \DateTime|string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = ($updatedAt instanceof \DateTime)
                        ? $updatedAt
                        : new \DateTime($updatedAt);
    }

    /**
     * persist
     *
     * @ORM\PrePersist
     *
     * @return void
     */
    public function persistTimestamp()
    {
        $this->setCreatedAt('now');
        $this->setUpdatedAt('now');
    }

    /**
     * update
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function updateTimestamp()
    {
        $this->setUpdatedAt('now');
    }
}
