<?php

declare(strict_types=1);

namespace App\Form;

use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;

class AdvanceSaleForTheaterUserForm extends AbstractAdvanceSaleForm
{
    public function __construct(EntityManager $em)
    {
        $this->type           = self::TYPE_EDIT;
        $this->em             = $em;
        $this->ticketFieldset = new AdvanceTicketFieldset(true);

        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'tickets',
            'type' => 'Collection',
            'options' => [
                'target_element' => $this->ticketFieldset,
            ],
        ]);

        $inputFilter = new InputFilter();

        // fieldsetのinputfilterが消えてしまう？ので設定しない
        // 1件以上必要な場合はどうする？
        // $inputFilter->add([
        //     'name' => 'tickets',
        // ]);

        $this->setInputFilter($inputFilter);
    }
}
