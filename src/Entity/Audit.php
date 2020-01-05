<?php

namespace App\Entity;

use App\Entity\Traits\ObservationsTrait;
use App\Enum\AuditTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Audit.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\AuditRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class Audit extends AbstractBase
{
    use ObservationsTrait;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $beginDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endDate;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=1})
     */
    protected $diagramType = 1;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=4000, nullable=true)
     */
    private $tools;

    /**
     * @var Windmill
     *
     * @ORM\ManyToOne(targetEntity="Windmill", inversedBy="audits")
     * @ORM\JoinColumn(name="windmill_id", referencedColumnName="id")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $windmill;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AuditWindmillBlade", mappedBy="audit", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid
     */
    private $auditWindmillBlades;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="audits_users", joinColumns={@ORM\JoinColumn(name="audit_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")})
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $operators;

    /**
     * @var Windfarm
     *
     * @ORM\ManyToOne(targetEntity="Windfarm")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $windfarm;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $customer;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=0})
     */
    protected $language = 0;

    /**
     * @var WorkOrder
     *
     * @ORM\ManyToOne(targetEntity="WorkOrder", inversedBy="audits")
     * @ORM\JoinColumn(name="workorder_id", referencedColumnName="id", nullable=true)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $workOrder;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $hasWorkOrder;

    /**
     * Methods.
     */

    /**
     * Audit constructor.
     */
    public function __construct()
    {
        $this->auditWindmillBlades = new ArrayCollection();
        $this->operators = new ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * @return string
     */
    public function getPdfBeginDateString()
    {
        return $this->beginDate ? $this->getBeginDate()->format('d/m/Y') : '---';
    }

    /**
     * @param \DateTime $beginDate
     *
     * @return Audit
     */
    public function setBeginDate(\DateTime $beginDate)
    {
        $this->beginDate = $beginDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getPdfEndDateString()
    {
        return $this->endDate ? $this->getEndDate()->format('d/m/Y') : '---';
    }

    /**
     * @param \DateTime|null $endDate
     *
     * @return Audit
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getDiagramType()
    {
        return $this->diagramType;
    }

    /**
     * @param int $diagramType
     *
     * @return $this
     */
    public function setDiagramType($diagramType)
    {
        $this->diagramType = $diagramType;

        return $this;
    }

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
        return AuditTypeEnum::getStringValue($this);
    }

    /**
     * @return string
     */
    public function getTypeStringLocalized()
    {
        return AuditTypeEnum::getStringLocalizedValue($this);
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * @param string $tools
     *
     * @return $this
     */
    public function setTools($tools)
    {
        $this->tools = $tools;

        return $this;
    }

    /**
     * @return Windmill
     */
    public function getWindmill()
    {
        return $this->windmill;
    }

    /**
     * @param Windmill $windmill
     *
     * @return $this
     */
    public function setWindmill($windmill)
    {
        $this->windmill = $windmill;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAuditWindmillBlades()
    {
        return $this->auditWindmillBlades;
    }

    /**
     * @param ArrayCollection $auditWindmillBlades
     *
     * @return $this
     */
    public function setAuditWindmillBlades($auditWindmillBlades)
    {
        $this->auditWindmillBlades = $auditWindmillBlades;

        return $this;
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return $this
     */
    public function addAuditWindmillBlade(AuditWindmillBlade $auditWindmillBlade)
    {
        $auditWindmillBlade->setAudit($this);
        $this->auditWindmillBlades->add($auditWindmillBlade);

        return $this;
    }

    /**
     * @param AuditWindmillBlade $auditWindmillBlade
     *
     * @return $this
     */
    public function removeAuditWindmillBlade(AuditWindmillBlade $auditWindmillBlade)
    {
        $this->auditWindmillBlades->removeElement($auditWindmillBlade);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOperators()
    {
        return $this->operators;
    }

    /**
     * @param ArrayCollection $operators
     *
     * @return Audit
     */
    public function setOperators($operators)
    {
        $this->operators = $operators;

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addOperator(User $user)
    {
        $this->operators->add($user);

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function removeOperator(User $user)
    {
        $this->operators->removeElement($user);

        return $this;
    }

    /**
     * @return Windfarm
     */
    public function getWindfarm()
    {
        return $this->windfarm;
    }

    /**
     * @param Windfarm $windfarm
     *
     * @return Audit
     */
    public function setWindfarm($windfarm)
    {
        $this->windfarm = $windfarm;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return Audit
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return int
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param int $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function toStringWithoutJoins()
    {
        return $this->id ? $this->getBeginDate()->format('d/m/Y').' · '.$this->getWindfarm()->getCustomer()->getName().' · '.$this->getWindfarm()->getName().' · ' : '---';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getBeginDate()->format('d/m/Y').' · '.$this->getWindmill() : '---';
    }

    /**
     * @return WorkOrder
     */
    public function getWorkOrder(): ?WorkOrder
    {
        return $this->workOrder;
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return Audit
     */
    public function setWorkOrder(WorkOrder $workOrder): Audit
    {
        $this->workOrder = $workOrder;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasWorkOrder(): bool
    {
        return $this->hasWorkOrder;
    }

    /**
     * @param bool $hasWorkOrder
     *
     * @return Audit
     */
    public function setHasWorkOrder(bool $hasWorkOrder): Audit
    {
        $this->hasWorkOrder = $hasWorkOrder;

        return $this;
    }
}
