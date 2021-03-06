<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity\Title;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class TitleForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct();

        $this->add([
            'name' => 'name',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'name_kana',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'sub_title',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'image',
            'type' => 'File',
        ]);

        // @todo 編集かつ画像があるとき
        $this->add([
            'name' => 'delete_image',
            'type' => 'Checkbox',
        ]);

        $this->add([
            'name' => 'credit',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'catchcopy',
            'type' => 'Textarea',
        ]);

        $this->add([
            'name' => 'introduction',
            'type' => 'Textarea',
        ]);

        $this->add([
            'name' => 'director',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'cast',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'publishing_expected_date',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'not_exist_publishing_expected_date',
            'type' => 'Checkbox',
        ]);

        $this->add([
            'name' => 'official_site',
            'type' => 'Url',
        ]);

        $this->add([
            'name' => 'rating',
            'type' => 'Select',
            'options' => [
                'value_options' => $this->getRatingChoices(),
            ],
        ]);

        $this->add([
            'name' => 'universal',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->getUniversalChoices(),
            ],
        ]);

        $this->add([
            'name' => 'chever_code',
            'type' => 'Text',
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'name_kana',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'sub_title',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'image',
            'required' => false,
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

        // @todo 編集かつ画像があるとき
        $inputFilter->add([
            'name' => 'delete_image',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'credit',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'catchcopy',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'introduction',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'director',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'cast',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'publishing_expected_date',
            'required' => true, // 未定ならばfalseにする
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'not_exist_publishing_expected_date',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'official_site',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'rating',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'universal',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'chever_code',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }

    public function isValid(): bool
    {
        $this->preValidator($this->data);

        return parent::isValid();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function preValidator(array $data): void
    {
        if (isset($data['not_exist_publishing_expected_date'])) {
            $this->getInputFilter()->get('publishing_expected_date')->setRequired(false);
        }
    }

    /**
     * @return array<int, string>
     */
    public function getRatingChoices(): array
    {
        return Title::getRatingTypes();
    }

    /**
     * @return array<int, string>
     */
    public function getUniversalChoices(): array
    {
        return Title::getUniversalTypes();
    }
}
