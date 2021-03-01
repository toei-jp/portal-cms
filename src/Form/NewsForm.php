<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity\News;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class NewsForm extends BaseForm
{
    public const TYPE_NEW  = 1;
    public const TYPE_EDIT = 2;

    /** @var int */
    protected $type;

    /** @var array<int, string> */
    protected $categoryChoices;

    public function __construct(int $type)
    {
        $this->type            = $type;
        $this->categoryChoices = News::$categories;

        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
    {
        if ($this->type === self::TYPE_EDIT) {
            $this->add([
                'name' => 'id',
                'type' => 'Hidden',
            ]);
        }

        $this->add([
            'name' => 'category',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->categoryChoices,
            ],
        ]);

        $this->add([
            'name' => 'title_id',
            'type' => 'Hidden',
        ]);

        // 作品名を表示するため
        $this->add([
            'name' => 'title_name',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'start_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'end_dt',
            'type' => 'Text', // Datepickerを入れるのでtextにする
        ]);

        $this->add([
            'name' => 'headline',
            'type' => 'Text',
        ]);

        $this->add([
            'name' => 'body',
            'type' => 'Textarea',
        ]);

        $this->add([
            'name' => 'image',
            'type' => 'File',
        ]);

        $inputFilter = new InputFilter();

        if ($this->type === self::TYPE_EDIT) {
            $inputFilter->add([
                'name' => 'id',
                'required' => true,
            ]);
        }

        $inputFilter->add([
            'name' => 'category',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'title_id',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'title_name',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'start_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'end_dt',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Date::class,
                    'options' => ['format' => 'Y/m/d H:i'],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'headline',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'body',
            'required' => true,
        ]);

        $inputFilter->add([
            'name' => 'image',
            'required' => ($this->type === self::TYPE_NEW),
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

    /**
     * @return array<int, string>
     */
    public function getCategoryChoices(): array
    {
        return $this->categoryChoices;
    }
}
