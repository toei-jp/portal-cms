<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdvanceSale entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\AdvanceSaleRepository")
 * @ORM\Table(name="advance_sale", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdvanceSale extends AbstractEntity
{
    use SavedUserTrait;
    use SoftDeleteTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected Theater $theater;

    /**
     * @ORM\ManyToOne(targetEntity="Title")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected Title $title;

    /** @ORM\Column(type="date", name="publishing_expected_date", nullable=true) */
    protected ?DateTime $publishingExpectedDate = null;

    /** @ORM\Column(type="string", name="publishing_expected_date_text", nullable=true) */
    protected ?string $publishingExpectedDateText = null;

    /**
     * @ORM\OneToMany(targetEntity="AdvanceTicket", mappedBy="advanceSale", indexBy="id")
     *
     * @var Collection<AdvanceTicket>
     */
    protected Collection $advanceTickets;

    public function __construct()
    {
        $this->advanceTickets = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTheater(): Theater
    {
        return $this->theater;
    }

    public function setTheater(Theater $theater): void
    {
        $this->theater = $theater;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(Title $title): void
    {
        $this->title = $title;
    }

    public function getPublishingExpectedDate(): ?DateTime
    {
        return $this->publishingExpectedDate;
    }

    /**
     * @param DateTime|string|null $publishingExpectedDate
     */
    public function setPublishingExpectedDate($publishingExpectedDate): void
    {
        if (is_null($publishingExpectedDate) || ($publishingExpectedDate instanceof DateTime)) {
            $this->publishingExpectedDate = $publishingExpectedDate;
        } else {
            $this->publishingExpectedDate = new DateTime($publishingExpectedDate);
        }
    }

    public function getPublishingExpectedDateText(): ?string
    {
        return $this->publishingExpectedDateText;
    }

    public function setPublishingExpectedDateText(?string $publishingExpectedDateText): void
    {
        $this->publishingExpectedDateText = $publishingExpectedDateText;
    }

    /**
     * @return Collection<AdvanceTicket>
     */
    public function getAdvanceTickets(): Collection
    {
        return $this->advanceTickets;
    }

    /**
     * @return Collection<AdvanceTicket>
     */
    public function getActiveAdvanceTickets(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isDeleted', false));

        /**
         * matching()を使うとindexByオプションの設定が消えてしまう
         * https://github.com/doctrine/doctrine2/issues/4693
         */
        $tmpResults = $this->getAdvanceTickets()->matching($criteria);

        // idをindexにしたcollectionを作り直す
        $results = new ArrayCollection();

        foreach ($tmpResults as $tmp) {
            /** @var AdvanceTicket $tmp */
            $results->set($tmp->getId(), $tmp);
        }

        return $results;
    }
}
