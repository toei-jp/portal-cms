<?php

declare(strict_types=1);

namespace App\Form;

use App\ORM\Entity;
use Doctrine\ORM\EntityManager;
use Laminas\InputFilter\InputFilter;

class NewsFindForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;

    /** @var array<int, string> */
    protected $termChoices = [
        '1' => '掲出中',
        '2' => '掲出終了',
    ];

    /** @var array<int, string> */
    protected $categoryChoices;

    /** @var array<int, string>*/
    protected $pageChoices = [];

    /** @var array<int, string> */
    protected $theaterChoices = [];

    public function __construct(EntityManager $em)
    {
        $this->em              = $em;
        $this->categoryChoices = Entity\News::$categories;

        parent::__construct();

        $this->setup();
    }

    protected function setup(): void
    {
        $this->add([
            'name' => 'term',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->termChoices,
            ],
        ]);

        $this->add([
            'name' => 'category',
            'type' => 'Radio',
            'options' => [
                'value_options' => $this->categoryChoices,
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
            'name' => 'term',
            'required' => false,
        ]);

        $inputFilter->add([
            'name' => 'category',
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
    public function getTermChoices(): array
    {
        return $this->termChoices;
    }

    /**
     * @return array<int, string>
     */
    public function getCategoryChoices(): array
    {
        return $this->categoryChoices;
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
