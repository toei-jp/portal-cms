<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity\ShowingFormat;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class ShowingFormatFieldset extends Fieldset implements InputFilterProviderInterface
{
    /** @var array<int, string> */
    protected $systemChoices;

    /** @var array<int, string> */
    protected $voiceChoices;

    public function __construct()
    {
        parent::__construct('showing');

        $this->systemChoices = ShowingFormat::getSystemList();
        $this->voiceChoices  = ShowingFormat::getVoiceList();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'system',
            'type' => 'Select',
            'options' => [
                'empty_option' => '',
                'value_options' => $this->systemChoices,
            ],
        ]);

        $this->add([
            'name' => 'voice',
            'type' => 'Select',
            'options' => [
                'empty_option' => '',
                'value_options' => $this->voiceChoices,
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'system' => ['required' => true],
            'voice' => ['required' => true],
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getSystemChoices(): array
    {
        return $this->systemChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getVoiceChoices(): array
    {
        return $this->voiceChoices;
    }
}
