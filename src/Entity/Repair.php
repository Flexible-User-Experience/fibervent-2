<?php

namespace App\Entity;

use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Repair.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\RepairRepository")
 * ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @UniqueEntity("name")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class Repair extends AbstractBase
{
    use NameTrait;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? $this->getName() : '---';
    }
}
