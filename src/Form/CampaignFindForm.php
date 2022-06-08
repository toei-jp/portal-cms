<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity;
use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;

class CampaignFindForm extends BaseForm
{
    protected EntityManager $em;

    /** @var array<int, string> */
    protected array $statusChoices = [
        '1' => 'キャンペーン中',
        '2' => 'キャンペーン終了',
    ];

    /** @var array<int, string> */
    protected array $pageChoices = [];

    /** @var array<int, string> */
    protected array $theaterChoices = [];

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'status',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->statusChoices,
            ],
        ]);

        /** @var Entity\Page[] $pages */
        $pages = $this->em->getRepository(Entity\Page::class)->findActive();

        foreach ($pages as $page) {
            $this->pageChoices[$page->getId()] = $page->getNameJa();
        }

        $this->add([
            'name' => 'page',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->pageChoices,
            ],
        ]);

        /** @var Entity\Theater[] $theaters */
        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();

        foreach ($theaters as $theater) {
            $this->theaterChoices[$theater->getId()] = $theater->getNameJa();
        }

        $this->add([
            'name' => 'theater',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->theaterChoices,
            ],
        ]);

        $inputFilter = new InputFilter();
        $inputFilter->add([
            'name' => 'status',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'page',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'theater',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }

    /**
     * @return array<int, string>
     */
    public function getStatusChoices(): array
    {
        return $this->statusChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getPageChoices(): array
    {
        return $this->pageChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getTheaterChoices(): array
    {
        return $this->theaterChoices;
    }
}
