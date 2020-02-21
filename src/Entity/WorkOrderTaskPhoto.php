<?php

namespace App\Entity;

use App\Entity\Traits\ImageTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * WorkOrderTaskPhoto.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\WorkOrderTaskPhotoRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 * @Vich\Uploadable
 */
class WorkOrderTaskPhoto extends AbstractBase
{
    use ImageTrait;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="blade", fileNameProperty="imageName")
     * @Assert\File(
     *     maxSize = "10M",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth = 1200)
     */
    private $imageFile;

    /**
     * @var WorkOrderTask
     *
     * @ORM\ManyToOne(targetEntity="WorkOrderTask", inversedBy="photos")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $workOrderTask;

    /**
     * Methods.
     */

    /**
     * @return WorkOrderTask
     */
    public function getWorkOrderTask()
    {
        return $this->workOrderTask;
    }

    /**
     * @param WorkOrderTask $workOrderTask
     *
     * @return $this
     */
    public function setWorkOrderTask(WorkOrderTask $workOrderTask)
    {
        $this->workOrderTask = $workOrderTask;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getImageName() ? $this->getImageName() : '---';
    }
}
