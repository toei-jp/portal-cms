<?php

declare(strict_types=1);

namespace App\Form;

use Doctrine\ORM\EntityManager;

abstract class AbstractAdvanceSaleForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    protected int $type;

    protected EntityManager $em;

    protected AdvanceTicketFieldset $ticketFieldset;

    /** @var array<int, string> */
    protected array $theaterChoices = [];

    /**
     * @return array<int, string>
     */
    public function getTicketTypeChoices(): array
    {
        return $this->ticketFieldset->getTypeChoices();
    }

    /**
     * @return array<int, string>
     */
    public function getTicketSpecialGiftStockChoices(): array
    {
        return $this->ticketFieldset->getSpecialGiftStockChoices();
    }
}
