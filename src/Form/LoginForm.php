<?php

/**
 * LoginForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Form;

use Laminas\InputFilter\InputFilter;
use Toei\PortalAdmin\ValidatorTranslator;

/**
 * Login form class
 */
class LoginForm extends BaseForm
{
    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Password',
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'password',
            'required' => true,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
