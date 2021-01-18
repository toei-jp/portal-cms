<?php

namespace App\ORM\Entity;

/**
 * SoftDelete trait
 *
 * 論理削除に関する機能。
 */
trait SoftDeleteTrait
{
    /**
     * is_deleted
     *
     * @ORM\Column(type="boolean", name="is_deleted", options={"default":false})
     *
     * @var bool
     */
    protected $isDeleted = false;

    /**
     * get is_deleted
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * set is_deleted
     *
     * @param bool $isDeleted
     * @return void
     */
    public function setIsDeleted(bool $isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * is deleted
     *
     * alias getIsDeleted()
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->getIsDeleted();
    }
}
