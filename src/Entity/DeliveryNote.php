<?php

namespace App\Entity;

use App\Enum\RepairAccessTypeEnum;
use App\Enum\RepairWindmillSectionEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DeliveryNote.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\DeliveryNoteRepository")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class DeliveryNote extends AbstractBase
{
    /**
     * @var WorkOrder
     *
     * @ORM\ManyToOne(targetEntity="WorkOrder", inversedBy="deliveryNotes", cascade={"persist"})
     */
    private $workOrder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var array
     *
     * @ORM\Column(name="repair_windmill_sections", type="json_array", nullable=true)
     */
    private $repairWindmillSections = [];

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="admin_user_id", referencedColumnName="id", nullable=false)
     */
    private $teamLeader;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="team_technician_1_user_id", referencedColumnName="id", nullable=true)
     */
    private $teamTechnician1;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="team_technician_2_user_id", referencedColumnName="id", nullable=true)
     */
    private $teamTechnician2;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="team_technician_3_user_id", referencedColumnName="id", nullable=true)
     */
    private $teamTechnician3;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="team_technician_4_user_id", referencedColumnName="id", nullable=true)
     */
    private $teamTechnician4;

    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id", nullable=true)
     */
    private $vehicle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $craneCompany;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $craneDriver;

    /**
     * @var array
     *
     * @ORM\Column(name="repair_access_types", type="json_array", nullable=true)
     */
    private $repairAccessTypes = [];

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $observations;

    /**
     * @var DeliveryNoteTimeRegister[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="DeliveryNoteTimeRegister", mappedBy="deliveryNote", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $timeRegisters;

    /**
     * @var NonStandardUsedMaterial[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="NonStandardUsedMaterial", mappedBy="deliveryNote", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $nonStandardUsedMaterials;

    /**
     * @var WorkOrderTask[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="WorkOrderTask", mappedBy="deliveryNotes", cascade={"persist"})
     */
    private $workOrderTasks;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalTripHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalWorkHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalStopHours;

    /**
     * Methods.
     */

    /**
     * @return WorkOrder
     */
    public function getWorkOrder()
    {
        return $this->workOrder;
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return DeliveryNote
     */
    public function setWorkOrder(WorkOrder $workOrder): DeliveryNote
    {
        $this->workOrder = $workOrder;

        return $this;
    }

    /**
     * @return \DateTime
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
     * @param \DateTime $date
     *
     * @return DeliveryNote
     */
    public function setDate(\DateTime $date): DeliveryNote
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return array
     */
    public function getRepairWindmillSections()
    {
        return $this->repairWindmillSections;
    }

    /**
     * @return string
     */
    public function getRepairWindmillSectionsString(): string
    {
        $repairWindmillSectionsString = [];
        foreach ($this->getRepairWindmillSections() as $repairWindmillSection) {
            $repairWindmillSectionsString[] = RepairWindmillSectionEnum::getTranslatedDecodedStringFromType($repairWindmillSection);
        }

        return join(', ', $repairWindmillSectionsString);
    }

    /**
     * @return array
     */
    public function getRepairWindmillSectionsStringsArray(): array
    {
        $repairWindmillSectionsString = [];
        foreach ($this->getRepairWindmillSections() as $repairWindmillSection) {
            $repairWindmillSectionsString[] = RepairWindmillSectionEnum::getDecodedStringFromType(
                $repairWindmillSection
            );

        }

        return $repairWindmillSectionsString;
    }

    /**
     * @param array $repairWindmillSections
     *
     * @return DeliveryNote
     */
    public function setRepairWindmillSections(array $repairWindmillSections): DeliveryNote
    {
        $this->repairWindmillSections = $repairWindmillSections;

        return $this;
    }

    /**
     * @param int $repairWindmillSection
     *
     * @return DeliveryNote
     */
    public function addRepairWindmillSection(int $repairWindmillSection): DeliveryNote
    {
        if (false === ($key = array_search($repairWindmillSection, $this->repairWindmillSections))) {
            $this->repairWindmillSections[] = $repairWindmillSection;
        }

        return $this;
    }

    /**
     * @param int $repairWindmillSection
     *
     * @return DeliveryNote
     */
    public function removeRepairWindmillSection(int $repairWindmillSection): DeliveryNote
    {
        if (false !== ($key = array_search($repairWindmillSection, $this->repairWindmillSections))) {
            unset($this->repairWindmillSections[$key]);
        }

        return $this;
    }

    /**
     * @return User
     */
    public function getTeamLeader()
    {
        return $this->teamLeader;
    }

    /**
     * @param User $teamLeader
     *
     * @return DeliveryNote
     */
    public function setTeamLeader(User $teamLeader): DeliveryNote
    {
        $this->teamLeader = $teamLeader;

        return $this;
    }

    /**
     * @return User
     */
    public function getTeamTechnician1()
    {
        return $this->teamTechnician1;
    }

    /**
     * @param User $teamTechnician1
     *
     * @return DeliveryNote
     */
    public function setTeamTechnician1(User $teamTechnician1): DeliveryNote
    {
        $this->teamTechnician1 = $teamTechnician1;

        return $this;
    }

    /**
     * @return User
     */
    public function getTeamTechnician2()
    {
        return $this->teamTechnician2;
    }

    /**
     * @param User $teamTechnician2
     *
     * @return DeliveryNote
     */
    public function setTeamTechnician2(User $teamTechnician2): DeliveryNote
    {
        $this->teamTechnician2 = $teamTechnician2;

        return $this;
    }

    /**
     * @return User
     */
    public function getTeamTechnician3()
    {
        return $this->teamTechnician3;
    }

    /**
     * @param User $teamTechnician3
     *
     * @return DeliveryNote
     */
    public function setTeamTechnician3(User $teamTechnician3): DeliveryNote
    {
        $this->teamTechnician3 = $teamTechnician3;

        return $this;
    }

    /**
     * @return User
     */
    public function getTeamTechnician4()
    {
        return $this->teamTechnician4;
    }

    /**
     * @param User $teamTechnician4
     *
     * @return DeliveryNote
     */
    public function setTeamTechnician4(User $teamTechnician4): DeliveryNote
    {
        $this->teamTechnician4 = $teamTechnician4;

        return $this;
    }

    /**
     * @return Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return DeliveryNote
     */
    public function setVehicle(Vehicle $vehicle): DeliveryNote
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return string
     */
    public function getCraneCompany()
    {
        return $this->craneCompany;
    }

    /**
     * @param string $craneCompany
     *
     * @return DeliveryNote
     */
    public function setCraneCompany(string $craneCompany): DeliveryNote
    {
        $this->craneCompany = $craneCompany;

        return $this;
    }

    /**
     * @return string
     */
    public function getCraneDriver()
    {
        return $this->craneDriver;
    }

    /**
     * @param string $craneDriver
     *
     * @return DeliveryNote
     */
    public function setCraneDriver(string $craneDriver): DeliveryNote
    {
        $this->craneDriver = $craneDriver;

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
     * @return $this
     */
    public function setRepairAccessTypes(array $repairAccessTypes): DeliveryNote
    {
        $this->repairAccessTypes = $repairAccessTypes;

        return $this;
    }

    /**
     * @param int $repairAccessType
     *
     * @return $this
     */
    public function addRepairAccessType(int $repairAccessType): DeliveryNote
    {
        if (false === ($key = array_search($repairAccessType, $this->repairAccessTypes))) {
            $this->repairAccessTypes[] = $repairAccessType;
        }

        return $this;
    }

    /**
     * @param int $repairAccessType
     *
     * @return $this
     */
    public function removeRepairAccessType(int $repairAccessType): DeliveryNote
    {
        if (false !== ($key = array_search($repairAccessType, $this->repairAccessTypes))) {
            unset($this->repairAccessTypes[$key]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRepairAccessTypesString(): string
    {
        $repairAccessTypesString = [];
        foreach ($this->getRepairAccessTypes() as $repairAccessType) {
            $repairAccessTypesString[] = RepairAccessTypeEnum::getTranslatedDecodedStringFromType($repairAccessType);
        }

        return join(', ', $repairAccessTypesString);
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
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * @param string $observations
     *
     * @return DeliveryNote
     */
    public function setObservations(string $observations): DeliveryNote
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @return DeliveryNoteTimeRegister[]|ArrayCollection
     */
    public function getTimeRegisters()
    {
        return $this->timeRegisters;
    }

    /**
     * @param DeliveryNoteTimeRegister $timeRegister
     *
     * @return $this
     */
    public function addTimeRegister(DeliveryNoteTimeRegister $timeRegister)
    {
        $timeRegister->setDeliveryNote($this);
        $this->timeRegisters->add($timeRegister);

        return $this;
    }

    /**
     * @param DeliveryNoteTimeRegister[]|ArrayCollection $timeRegisters
     *
     * @return DeliveryNote
     */
    public function setTimeRegisters($timeRegisters): DeliveryNote
    {
        $this->timeRegisters = $timeRegisters;

        return $this;
    }

    /**
     * @return NonStandardUsedMaterial[]|ArrayCollection
     */
    public function getNonStandardUsedMaterials()
    {
        return $this->nonStandardUsedMaterials;
    }

    /**
     * @param NonStandardUsedMaterial $nonStandardUsedMaterial
     *
     * @return $this
     */
    public function addNonStandardUsedMaterial(NonStandardUsedMaterial $nonStandardUsedMaterial)
    {
        $nonStandardUsedMaterial->setDeliveryNote($this);
        $this->nonStandardUsedMaterials->add($nonStandardUsedMaterial);

        return $this;
    }

    /**
     * @param NonStandardUsedMaterial[]|ArrayCollection $nonStandardUsedMaterials
     *
     * @return DeliveryNote
     */
    public function setNonStandardUsedMaterials($nonStandardUsedMaterials): DeliveryNote
    {
        $this->nonStandardUsedMaterials = $nonStandardUsedMaterials;

        return $this;
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
        $this->workOrderTasks->add($workOrderTask);

        return $this;
    }

    /**
     * @param WorkOrderTask $workOrderTask
     *
     * @return $this
     */
    public function removeWorkOrderTask(WorkOrderTask $workOrderTask)
    {
        $this->workOrderTasks->removeElement($workOrderTask);

        return $this;
    }

    /**
     * @param WorkOrderTask[]|ArrayCollection $workOrderTasks
     *
     * @return DeliveryNote
     */
    public function setWorkOrderTasks($workOrderTasks)
    {
        $this->workOrderTasks = $workOrderTasks;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalTripHours()
    {
        return $this->totalTripHours;
    }

    /**
     * @param float $totalTripHours
     *
     * @return DeliveryNote
     */
    public function setTotalTripHours(float $totalTripHours): DeliveryNote
    {
        $this->totalTripHours = $totalTripHours;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalWorkHours()
    {
        return $this->totalWorkHours;
    }

    /**
     * @param float $totalWorkHours
     *
     * @return DeliveryNote
     */
    public function setTotalWorkHours(float $totalWorkHours): DeliveryNote
    {
        $this->totalWorkHours = $totalWorkHours;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalStopHours()
    {
        return $this->totalStopHours;
    }

    /**
     * @param float $totalStopHours
     *
     * @return DeliveryNote
     */
    public function setTotalStopHours(float $totalStopHours): DeliveryNote
    {
        $this->totalStopHours = $totalStopHours;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->workOrder.' / '.($this->date ? $this->date->format('d/m/Y') : '');
    }
}
