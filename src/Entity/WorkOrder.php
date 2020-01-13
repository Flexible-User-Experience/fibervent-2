<?php

namespace App\Entity;

use App\Enum\RepairAccessTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WorkOrder.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\WorkOrderRepository")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class WorkOrder extends AbstractBase
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $projectNumber;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isFromAudit = false;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    private $customer;

    /**
     * @var Windfarm
     *
     * @ORM\ManyToOne(targetEntity="Windfarm")
     * @ORM\JoinColumn(name="windfarm_id", referencedColumnName="id", nullable=true)
     */
    private $windfarm;

    /**
     * @var Audit[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Audit", mappedBy="workOrder")
     */
    private $audits;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $certifyingCompanyName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $certifyingCompanyContactPerson;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $certifyingCompanyPhone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $certifyingCompanyEmail;

    /**
     * @var array
     *
     * @ORM\Column(name="repair_access_types", type="json_array", nullable=true)
     */
    private $repairAccessTypes = [];

    /**
     * @var WorkOrderTask[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WorkOrderTask", mappedBy="workOrder", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $workOrderTasks;

    /**
     * @var DeliveryNote[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="DeliveryNote", mappedBy="workOrder", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $deliveryNotes;

    /**
     * Methods.
     */

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     *
     * @return $this
     */
    public function setStatus(?int $status): WorkOrder
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getProjectNumber()
    {
        return $this->projectNumber;
    }

    /**
     * @param string $projectNumber
     *
     * @return WorkOrder
     */
    public function setProjectNumber($projectNumber): WorkOrder
    {
        $this->projectNumber = $projectNumber;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFromAudit(): bool
    {
        return $this->isFromAudit;
    }

    /**
     * @param bool $isFromAudit
     *
     * @return WorkOrder
     */
    public function setIsFromAudit($isFromAudit): WorkOrder
    {
        $this->isFromAudit = $isFromAudit;

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
     * @return WorkOrder
     */
    public function setCustomer(Customer $customer): WorkOrder
    {
        $this->customer = $customer;

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
     * @return WorkOrder
     */
    public function setWindfarm(Windfarm $windfarm): WorkOrder
    {
        $this->windfarm = $windfarm;

        return $this;
    }

    /**
     * @return string
     */
    public function getCertifyingCompanyName()
    {
        return $this->certifyingCompanyName;
    }

    /**
     * @param string $certifyingCompanyName
     *
     * @return WorkOrder
     */
    public function setCertifyingCompanyName($certifyingCompanyName): WorkOrder
    {
        $this->certifyingCompanyName = $certifyingCompanyName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCertifyingCompanyContactPerson()
    {
        return $this->certifyingCompanyContactPerson;
    }

    /**
     * @param string $certifyingCompanyContactPerson
     *
     * @return WorkOrder
     */
    public function setCertifyingCompanyContactPerson($certifyingCompanyContactPerson): WorkOrder
    {
        $this->certifyingCompanyContactPerson = $certifyingCompanyContactPerson;

        return $this;
    }

    /**
     * @return string
     */
    public function getCertifyingCompanyPhone()
    {
        return $this->certifyingCompanyPhone;
    }

    /**
     * @param string $certifyingCompanyPhone
     *
     * @return WorkOrder
     */
    public function setCertifyingCompanyPhone($certifyingCompanyPhone): WorkOrder
    {
        $this->certifyingCompanyPhone = $certifyingCompanyPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getCertifyingCompanyEmail()
    {
        return $this->certifyingCompanyEmail;
    }

    /**
     * @param string $certifyingCompanyEmail
     *
     * @return WorkOrder
     */
    public function setCertifyingCompanyEmail($certifyingCompanyEmail): WorkOrder
    {
        $this->certifyingCompanyEmail = $certifyingCompanyEmail;

        return $this;
    }

    /**
     * @return array
     */
    public function getRepairAccessTypes()
    {
        return $this->repairAccessTypes;
    }

    /**
     * @param array $repairAccessTypes
     *
     * @return WorkOrder
     */
    public function setRepairAccessTypes($repairAccessTypes): WorkOrder
    {
        $this->repairAccessTypes = $repairAccessTypes;

        return $this;
    }

    /**
     * @param int $repairAccessType
     *
     * @return WorkOrder
     */
    public function addRepairAccessType($repairAccessType): WorkOrder
    {
        if (false === ($key = array_search($repairAccessType, $this->repairAccessTypes))) {
            $this->repairAccessTypes[] = $repairAccessType;
        }

        return $this;
    }

    /**
     * @param int $repairAccessType
     *
     * @return WorkOrder
     */
    public function removeRepairAccessType($repairAccessType): WorkOrder
    {
        if (false !== ($key = array_search($repairAccessType, $this->repairAccessTypes))) {
            unset($this->repairAccessTypes[$key]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRepairAccessTypesString(): array
    {
        $repairAccessTypes = $this->getRepairAccessTypes();
        $repairAccessTypesString = [];
        foreach ($repairAccessTypes as $repairAccessType) {
            $repairAccessTypesString[] = RepairAccessTypeEnum::getDecodedStringFromType($repairAccessType);
        }

        return $repairAccessTypesString;
    }

    /**
     * @return array
     */
    public function getRepairAccessTypesStringsArray(): array
    {
        $repairAccessTypesString = [];
        foreach ($this->getRepairAccessTypes() as $repairAccessType) {
            $repairAccessTypesString[] = RepairAccessTypeEnum::getDecodedStringFromType($repairAccessType);
        }

        return $repairAccessTypesString;
    }

    /**
     * @return WorkOrderTask[]|ArrayCollection
     */
    public function getWorkOrderTasks()
    {
        return $this->workOrderTasks;
    }

    /**
     * @param WorkOrderTask $workOrderTask
     *
     * @return $this
     */
    public function addWorkOrderTask(WorkOrderTask $workOrderTask)
    {
        $workOrderTask->setWorkOrder($this);
        $this->workOrderTasks->add($workOrderTask);

        return $this;
    }

    /**
     * @param WorkOrderTask[]|ArrayCollection $workOrderTasks
     *
     * @return WorkOrder
     */
    public function setWorkOrderTasks($workOrderTasks)
    {
        $this->workOrderTasks = $workOrderTasks;

        return $this;
    }

    /**
     * @return DeliveryNote[]|ArrayCollection
     */
    public function getDeliveryNotes()
    {
        return $this->deliveryNotes;
    }

    /**
     * @param DeliveryNote[]|ArrayCollection $deliveryNotes
     *
     * @return WorkOrder
     */
    public function setDeliveryNotes($deliveryNotes)
    {
        $this->deliveryNotes = $deliveryNotes;

        return $this;
    }

    /**
     * @return Audit[]|ArrayCollection
     */
    public function getAudits()
    {
        return $this->audits;
    }

    /**
     * @param Audit $audit
     *
     * @return $this
     */
    public function addAudit(Audit $audit)
    {
        $audit->setWorkOrder($this);

        return $this;
    }

    /**
     * @param Audit[]|ArrayCollection $audits
     *
     * @return WorkOrder
     */
    public function setAudits($audits)
    {
        $this->audits = $audits;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getProjectNumber() ? (string) $this->getProjectNumber() : '';
    }
}
