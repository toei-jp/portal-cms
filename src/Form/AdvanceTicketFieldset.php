<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity\AdvanceTicket;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator;

class AdvanceTicketFieldset extends Fieldset implements InputFilterProviderInterface
{
    protected bool $isTheaterUser;

    /** @var array<int, string> */
    protected array $typeChoices;

    /** @var array<int, string> */
    protected array $specialGiftStockChoices;

    public function __construct(bool $isTheaterUser)
    {
        parent::__construct('advance_ticket');

        $this->isTheaterUser           = $isTheaterUser;
        $this->typeChoices             = AdvanceTicket::getTypes();
        $this->specialGiftStockChoices = AdvanceTicket::getSpecialGiftStockList();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'delete_special_gift_image',
                'type' => 'Hidden',
            ]);
        }

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'release_dt',
                'type' => 'Text', // Datepickerを入れるのでtextにする
            ]);
        }

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'release_dt_text',
                'type' => 'Text',
            ]);
        }

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'is_sales_end',
                'type' => 'Checkbox',
                'options' => [
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
            ]);
        }

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'type',
                'type' => 'Radio',
                'options' => [
                    'value_options' => $this->typeChoices,
                ],
            ]);
        }

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'price_text',
                'type' => 'Text',
            ]);
        }

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'special_gift',
                'type' => 'Textarea',
            ]);
        }

        $this->add([
            'name' => 'special_gift_stock',
            'type' => 'Select',
            'options' => [
                'empty_option' => '',
                'value_options' => $this->specialGiftStockChoices,
            ],
        ]);

        if (! $this->isTheaterUser) {
            $this->add([
                'name' => 'special_gift_image',
                'type' => 'File',
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getInputFilterSpecification(): array
    {
        $specification = [
            'id' => ['required' => false],
        ];

        if (! $this->isTheaterUser) {
            $specification['release_dt'] = [
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\Date::class,
                        'options' => ['format' => 'Y/m/d H:i'],
                    ],
                ],
            ];
        }

        if (! $this->isTheaterUser) {
            $specification['release_dt_text'] = ['required' => false];
        }

        if (! $this->isTheaterUser) {
            $specification['is_sales_end'] = ['required' => false];
        }

        if (! $this->isTheaterUser) {
            $specification['type'] = ['required' => true];
        }

        if (! $this->isTheaterUser) {
            $specification['price_text'] = ['required' => true];
        }

        if (! $this->isTheaterUser) {
            $specification['special_gift'] = ['required' => false];
        }

        $specification['special_gift_stock'] = ['required' => false];

        if (! $this->isTheaterUser) {
            $specification['special_gift_image'] = [
                'required' => false,
                'allow_empty' => true,
                'validators' => [
                    [
                        'name' => Validator\File\Size::class,
                        'options' => ['max' => '10MB'], // SASAKI-245
                    ],
                    [
                        'name' => Validator\File\MimeType::class,
                        'options' => [
                            'mimeType' => AdvanceSaleForm::$imageMimeTypes,
                        ],
                    ],
                ],
            ];

            $specification['delete_special_gift_image'] = ['required' => false];
        }

        return $specification;
    }

    /**
     * @return array<int, string>
     */
    public function getTypeChoices(): array
    {
        return $this->typeChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getSpecialGiftStockChoices(): array
    {
        return $this->specialGiftStockChoices;
    }
}
