<?php

declare(strict_types=1);

namespace App\Form\API;

use App\Form\BaseForm;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class EditorUploadForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
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
