<?php
/**
 * NewsPublicationForm.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Toei\PortalAdmin\Form;

use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

use Doctrine\ORM\EntityManager;

use Toei\PortalAdmin\ORM\Entity;

/**
 * NewsPublication form class
 */
class NewsPublicationForm extends BaseForm
{
    const TARGET_PAGE          = 'page';
    const TARGET_TEATER        = 'theater';

    /** @var string */
    protected $target;

    /** @var EntityManager */
    protected $em;

    /**
     * construct
     *
     * @param string $target
     * @param EntityManager $em
     */
    public function __construct(string $target, EntityManager $em)
    {
        if (!in_array($target, [self::TARGET_PAGE, self::TARGET_TEATER])) {
            throw new \InvalidArgumentException('invalid target.');
        }

        $this->target = $target;
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
        if ($this->target === self::TARGET_PAGE) {
            $this->add([
                'name' => 'page_id',
                'type' => 'Hidden',
            ]);
        } elseif ($this->target === self::TARGET_TEATER) {
            $this->add([
                'name' => 'theater_id',
                'type' => 'Hidden',
            ]);
        }

        $this->add([
            'name' => 'news_list',
            'type' => 'Collection',
            'options' => [
                'target_element' => [
                    'type' => PublicationNewsFieldset::class,
                ],
            ],
        ]);


        $inputFilter = new InputFilter();

        if ($this->target === self::TARGET_PAGE) {
            $pageIds = [];
            $pages = $this->em->getRepository(Entity\Page::class)->findActive();

            foreach ($pages as $page) {
                $pageIds[] = $page->getId();
            }

            $inputFilter->add([
                'name' => 'page_id',
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\InArray::class,
                        'options' => [
                            'haystack' => $pageIds,
                        ],
                    ],
                ],
            ]);
        } elseif ($this->target === self::TARGET_TEATER) {
            $theaterIds = [];
            $theaters = $this->em->getRepository(Entity\Theater::class)->findActive();

            foreach ($theaters as $theater) {
                $theaterIds[] = $theater->getId();
            }

            $inputFilter->add([
                'name' => 'theater_id',
                'required' => true,
                'validators' => [
                    [
                        'name' => Validator\InArray::class,
                        'options' => [
                            'haystack' => $theaterIds,
                        ],
                    ],
                ],
            ]);
        }

        $inputFilter->add([
            'name' => 'news_list',
            'required' => false,
        ]);

        $this->setInputFilter($inputFilter);
    }
}
