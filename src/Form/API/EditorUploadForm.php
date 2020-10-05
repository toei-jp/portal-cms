<?php

/**
 * EditorUploadForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Form\API;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;
use Toei\PortalAdmin\Form\BaseForm;

/**
 * EditorUpload form class
 */
class EditorUploadForm extends BaseForm
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
            'name' => 'file',
            'type' => 'File',
        ]);

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'file',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\File\Size::class,
                    'options' => ['max' => '10MB'], // SASAKI-245
                ],
                [
                    'name' => Validator\File\MimeType::class,
                    'options' => [
                        'mimeType' => self::$imageMimeTypes,
                    ],
                ],
            ],
        ]);

        $this->setInputFilter($inputFilter);
    }
}
