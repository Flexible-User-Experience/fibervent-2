<?php

namespace App\Entity;

use App\Enum\PresenceMonitoringCategoryEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * PresenceMonitoring.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\PresenceMonitoringRepository")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class PresenceMonitoring extends AbstractBase
{
    const NORMAL_HOURS_AMOUNT = 8.0;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="admin_user_id", referencedColumnName="id", nullable=false)
     */
    private $worker;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     * @Assert\Time
     */
    private $morningHourBegin;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     * @Assert\Time
     */
    private $morningHourEnd;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     * @Assert\Time
     */
    private $afternoonHourBegin;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     * @Assert\Time
     */
    private $afternoonHourEnd;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $normalHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $extraHours;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $category;

    /**
     * Methods.
     */

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDateString()
    {
        return $this->getDate()->format('d/m/Y');
    }

    /**
     * @param DateTime $date
     *
     * @return PresenceMonitoring
     */
    public function setDate(?DateTime $date): PresenceMonitoring
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return User
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param User $worker
     *
     * @return PresenceMonitoring
     */
    public function setWorker(User $worker): PresenceMonitoring
    {
        $this->worker = $worker;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMorningHourBegin()
    {
        return $this->morningHourBegin;
    }

    /**
     * @return string
     */
    public function getMorningHourBeginString()
    {
        return $this->getMorningHourBegin() ? $this->getMorningHourBegin()->format('H:i') : '--:--';
    }

    /**
     * @param DateTime $morningHourBegin
     *
     * @return PresenceMonitoring
     */
    public function setMorningHourBegin(?DateTime $morningHourBegin): PresenceMonitoring
    {
        $this->morningHourBegin = $morningHourBegin;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMorningHourEnd()
    {
        return $this->morningHourEnd;
    }

    /**
     * @return string
     */
    public function getMorningHourEndString()
    {
        return $this->getMorningHourEnd() ? $this->getMorningHourEnd()->format('H:i') : '--:--';
    }

    /**
     * @param DateTime $morningHourEnd
     *
     * @return PresenceMonitoring
     */
    public function setMorningHourEnd(?DateTime $morningHourEnd): PresenceMonitoring
    {
        $this->morningHourEnd = $morningHourEnd;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getAfternoonHourBegin()
    {
        return $this->afternoonHourBegin;
    }

    /**
     * @return string
     */
    public function getAfternoonHourBeginString()
    {
        return $this->getAfternoonHourBegin() ? $this->getAfternoonHourBegin()->format('H:i') : '--:--';
    }

    /**
     * @param DateTime $afternoonHourBegin
     *
     * @return PresenceMonitoring
     */
    public function setAfternoonHourBegin(?DateTime $afternoonHourBegin): PresenceMonitoring
    {
        $this->afternoonHourBegin = $afternoonHourBegin;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getAfternoonHourEnd()
    {
        return $this->afternoonHourEnd;
    }

    /**
     * @return string
     */
    public function getAfternoonHourEndString()
    {
        return $this->getAfternoonHourEnd() ? $this->getAfternoonHourEnd()->format('H:i') : '--:--';
    }

    /**
     * @param DateTime $afternoonHourEnd
     *
     * @return PresenceMonitoring
     */
    public function setAfternoonHourEnd(?DateTime $afternoonHourEnd): PresenceMonitoring
    {
        $this->afternoonHourEnd = $afternoonHourEnd;

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
     * @return float
     */
    public function getDifferenceBetweenEndAndBeginHoursInSeconds()
    {
        $result = 0.0;
        if ($this->getMorningHourBegin() && $this->getMorningHourEnd()) {
            $result += floatval($this->getMorningHourEnd()->getTimestamp() - $this->getMorningHourBegin()->getTimestamp());
        }
        if ($this->getAfternoonHourBegin() && $this->getAfternoonHourEnd()) {
            $result += floatval($this->getAfternoonHourEnd()->getTimestamp() - $this->getAfternoonHourBegin()->getTimestamp());
        }

        return $result;
    }

    /**
     * @return float
     */
    public function getDifferenceBetweenEndAndBeginHoursInDecimalHours()
    {
        return $this->getDifferenceBetweenEndAndBeginHoursInSeconds() / 60 / 60;
    }

    /**
     * @return float
     */
    public function getNormalHoursDifferenceFromTotal()
    {
        return $this->getTotalHours() <= self::NORMAL_HOURS_AMOUNT ? $this->getTotalHours() : self::NORMAL_HOURS_AMOUNT;
    }

    /**
     * @return float
     */
    public function getExtraHoursDifferenceFromTotal()
    {
        return $this->getTotalHours() > self::NORMAL_HOURS_AMOUNT ? $this->getTotalHours() - self::NORMAL_HOURS_AMOUNT : 0.0;
    }

    /**
     * @param float $totalHours
     *
     * @return PresenceMonitoring
     */
    public function setTotalHours(?float $totalHours): PresenceMonitoring
    {
        $this->totalHours = $totalHours;

        return $this;
    }

    /**
     * @return float
     */
    public function getNormalHours()
    {
        return $this->normalHours;
    }

    /**
     * @param float $normalHours
     *
     * @return PresenceMonitoring
     */
    public function setNormalHours(?float $normalHours): PresenceMonitoring
    {
        $this->normalHours = $normalHours;

        return $this;
    }

    /**
     * @return float
     */
    public function getExtraHours()
    {
        return $this->extraHours;
    }

    /**
     * @param float $extraHours
     *
     * @return PresenceMonitoring
     */
    public function setExtraHours(?float $extraHours): PresenceMonitoring
    {
        $this->extraHours = $extraHours;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getCategoryString()
    {
        return PresenceMonitoringCategoryEnum::getDecodedString($this->category);
    }

    /**
     * @param int $category
     *
     * @return PresenceMonitoring
     */
    public function setCategory($category): PresenceMonitoring
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param mixed                     $payload
     *
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getMorningHourBegin() && !$this->getMorningHourEnd()) {
            $context->buildViolation('Falta hora de salida mañana!')
                ->atPath('morningHourEnd')
                ->addViolation();
        }
        if ($this->getMorningHourBegin() && $this->getMorningHourEnd() && $this->getMorningHourBegin()->format('H:i') >= $this->getMorningHourEnd()->format('H:i')) {
            $context->buildViolation('La hora de salida mañana no puede ser menor o igual que la hora de entrada!')
                ->atPath('morningHourEnd')
                ->addViolation();
        }
        if ($this->getAfternoonHourBegin() && !$this->getAfternoonHourEnd()) {
            $context->buildViolation('Falta hora de salida tarde!')
                ->atPath('afternoonHourEnd')
                ->addViolation();
        }
        if ($this->getAfternoonHourBegin() && $this->getAfternoonHourEnd() && $this->getAfternoonHourBegin()->format('H:i') >= $this->getAfternoonHourEnd()->format('H:i')) {
            $context->buildViolation('La hora de salida tarde no puede ser menor o igual que la hora de entrada!')
                ->atPath('afternoonHourEnd')
                ->addViolation();
        }
        if ($this->getMorningHourBegin() && $this->getMorningHourEnd() && $this->getMorningHourBegin()->format('H:i') < $this->getMorningHourEnd()->format('H:i') && $this->getAfternoonHourBegin() && $this->getAfternoonHourEnd() && $this->getAfternoonHourBegin()->format('H:i') < $this->getAfternoonHourEnd()->format('H:i') && $this->getMorningHourEnd()->format('H:i') >= $this->getAfternoonHourBegin()->format('H:i')) {
            $context->buildViolation('La hora de entrada tarde no puede ser menor o igual que la hora de entrada mañana!')
                ->atPath('afternoonHourBegin')
                ->addViolation();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getDateString().' · '.$this->getWorker()->getFullnameCanonical() : '---';
    }
}
