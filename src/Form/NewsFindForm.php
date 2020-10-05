<?php

/**
 * NewsFindForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;
use Doctrine\ORM\EntityManager;
use Toei\PortalAdmin\ORM\Entity;

/**
 * News find form class
 */
class NewsFindForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $termChoices = [
        '1' => '掲出中',
        '2' => '掲出終了',
    ];

    /** @var array */
    protected $categoryChoices;

    /** @var array */
    protected $pageChoices = [];

    /** @var array */
    protected $theaterChoices = [];

    /**
     * construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em              = $em;
        $this->categoryChoices = Entity\News::$categories;

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

        $pages = $this->em->getRepository(Entity\Page::class)->findActive();

        foreach ($pages as $page) {
            /** @var Entity\Page $page */
            $this->pageChoices[$page->getId()] = $page->getNameJa();
        }

        $this->add([
            'name' => 'page',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->pageChoices,
            ],
        ]);

        $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();

        foreach ($theaters as $theater) {
            /** @var Entity\Theater $theater */
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
     * return term choices
     *
     * @return array
     */
    public function getTermChoices()
    {
        return $this->termChoices;
    }

    /**
     * return category choices
     *
     * @return array
     */
    public function getCategoryChoices()
    {
        return $this->categoryChoices;
    }

    /**
     * return page choices
     *
     * @return array
     */
    public function getPageChoices()
    {
        return $this->pageChoices;
    }

    /**
     * return theater choices
     *
     * @return array
     */
    public function getTheaterChoices()
    {
        return $this->theaterChoices;
    }
}
