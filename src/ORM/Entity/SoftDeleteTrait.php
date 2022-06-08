<?php

declare(strict_types=1);

namespace App\ORM\Entity;

trait SoftDeleteTrait
{
    /** @ORM\Column(type="boolean", name="is_deleted", options={"default":false}) */
    protected bool $isDeleted = false;

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * alias getIsDeleted()
     */
    public function isDeleted(): bool
    {
        return $this->getIsDeleted();
    }
}
