<?php

namespace App\Entity;

use App\Enum\TimeRegisterShiftEnum;
use App\Enum\TimeRegisterTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * DeliveryNoteTimeRegister.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\DeliveryNoteTimeRegisterRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class DeliveryNoteTimeRegister extends AbstractBase
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $shift;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time")
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time")
     */
    private $end;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHours;

    /**
     * @var DeliveryNote
     *
     * @ORM\ManyToOne(targetEntity="DeliveryNote", inversedBy="timeRegisters", cascade={"persist"})
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $deliveryNote;

    /**
     * Methods.
     */

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        return TimeRegisterTypeEnum::getStringValue($this);
    }

    /**
     * @param int $type
     *
     * @return DeliveryNoteTimeRegister
     */
    public function setType(int $type): DeliveryNoteTimeRegister
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * @return string
     */
    public function getShiftString()
    {
        return TimeRegisterShiftEnum::getStringValue($this);
    }

    /**
     * @param int $shift
     *
     * @return DeliveryNoteTimeRegister
     */
    public function setShift(int $shift): DeliveryNoteTimeRegister
    {
        $this->shift = $shift;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @param \DateTime $begin
     *
     * @return DeliveryNoteTimeRegister
     */
    public function setBegin(\DateTime $begin): DeliveryNoteTimeRegister
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     *
     * @return DeliveryNoteTimeRegister
     */
    public function setEnd(\DateTime $end): DeliveryNoteTimeRegister
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalHours()
    {
        return $this->totalHours;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getTotalHoursString()
    {
        $result = '---';
        $hours = $this->getTotalHours();
        if (!is_null($hours)) {
            if (is_integer($hours) || is_float($hours)) {
                $whole = floor($hours);
                $fraction = $hours - $whole;
                $minutes = 0;
                if (0.25 == $fraction) {
                    $minutes = 15;
                } elseif (0.5 == $fraction) {
                    $minutes = 30;
                } elseif (0.75 == $fraction) {
                    $minutes = 45;
                }
                $interval = new \DateInterval(sprintf('PT%dH%dM', intval($hours), $minutes));
                $result = $interval->format('%H:%I');
            }
        }

        return $result;
    }

    /**
     * @param float|null $totalHours
     *
     * @return DeliveryNoteTimeRegister
     */
    public function setTotalHours(?float $totalHours): DeliveryNoteTimeRegister
    {
        $this->totalHours = $totalHours;

        return $this;
    }

    /**
     * @return DeliveryNote
     */
    public function getDeliveryNote()
    {
        return $this->deliveryNote;
    }

    /**
     * @param DeliveryNote $deliveryNote
     *
     * @return DeliveryNoteTimeRegister
     */
    public function setDeliveryNote(DeliveryNote $deliveryNote): DeliveryNoteTimeRegister
    {
        $this->deliveryNote = $deliveryNote;

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!is_null($this->getBegin()) && !is_null($this->getEnd())) {
            if ($this->getBegin() instanceof \DateTime && $this->getEnd() instanceof \DateTime) {
                if ($this->getBegin()->format('H:i') >= $this->getEnd()->format('H:i')) {
                    $context->buildViolation('Hora inicial mayor o igual que hora final!')
                        ->atPath('begin')
                        ->addViolation()
                    ;
                }
            }
        }
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function __toString()
    {
        return $this->id ? $this->getId().' · '.($this->getDeliveryNote() ? $this->getDeliveryNote().' · ' : '').$this->getType().' · '.$this->getShift().' · '.$this->getTotalHoursString() : '---';
    }
}
