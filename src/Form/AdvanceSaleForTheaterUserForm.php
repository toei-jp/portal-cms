<?php
/**
 * AdvanceSaleForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

use Doctrine\ORM\EntityManager;

use Toei\PortalAdmin\ORM\Entity;

/**
 * AdvanceSale for theater user form class
 */
class AdvanceSaleForTheaterUserForm extends AbstractAdvanceSaleForm
{
    /**
     * construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->type = self::TYPE_EDIT;
        $this->em = $em;
        $this->ticketFieldset = new AdvanceTicketFieldset(true);

        parent::__construct();

        $this->setup();
    }

    /**
     * setup
     *
     * @return void
     */
    protected function setup()
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
