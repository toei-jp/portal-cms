<?php

/**
 * AbstractAdvanceSaleForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Form;

use Doctrine\ORM\EntityManager;

/**
 * Abstract AdvanceSale form class
 */
abstract class AbstractAdvanceSaleForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /** @var EntityManager */
    protected $em;

    /** @var AdvanceTicketFieldset */
    protected $ticketFieldset;

    /**@var array */
    protected $theaterChoices = [];

    /**
     * return ticket type choices
     *
     * @return array
     */
    public function getTicketTypeChoices()
    {
        return $this->ticketFieldset->getTypeChoices();
    }

    /**
     * return ticket special_gift_stock choices
     *
     * @return array
     */
    public function getTicketSpecialGiftStockChoices()
    {
        return $this->ticketFieldset->getSpecialGiftStockChoices();
    }
}
