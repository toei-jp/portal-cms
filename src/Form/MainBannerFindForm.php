<?php
/**
 * MainBannerFindForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * MainBanner find form class
 */
class MainBannerFindForm extends BaseForm
{
    /**
     * construct
     */
    public function __construct()
    {
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
            'name' => 'name',
            'type' => 'Text',
        ]);
        
        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => false,
        ]);
        
        $this->setInputFilter($inputFilter);
    }
}
