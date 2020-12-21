<?php

namespace Toei\PortalAdmin\Form;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;
use Doctrine\ORM\EntityManager;
use Toei\PortalAdmin\ORM\Entity;

/**
 * Campaign find form class
 */
class CampaignFindForm extends BaseForm
{
    /** @var EntityManager */
    protected $em;

    protected $statusChoices  = [
        '1' => 'キャンペーン中',
        '2' => 'キャンペーン終了',
    ];
    protected $pageChoices    = [];
    protected $theaterChoices = [];

    /**
     * construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;

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
            'name' => 'status',
            'type' => 'MultiCheckbox',
            'options' => [
                'value_options' => $this->statusChoices,
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
     * return status choices
     *
     * @return array
     */
    public function getStatusChoices()
    {
        return $this->statusChoices;
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
