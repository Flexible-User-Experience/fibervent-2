<?php

namespace App\Entity;

use App\Entity\Traits\ModelTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Blade.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\BladeRepository")
 * ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @UniqueEntity("model")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class Blade extends AbstractBase
{
    use ModelTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="float", precision=2)
     * @Assert\GreaterThan(value=1)
     */
    private $length;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $material;

    /**
     * Methods.
     */

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     *
     * @return Blade
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return string
     */
    public function getQ0LengthString()
    {
        return number_format(0, 2, ',', '.').'m';
    }

    /**
     * @return string
     */
    public function getQ1LengthString()
    {
        return number_format($this->getQuarterLength(1), 2, ',', '.').'m';
    }

    /**
     * @return string
     */
    public function getQ2LengthString()
    {
        return number_format($this->getQuarterLength(2), 2, ',', '.').'m';
    }

    /**
     * @return string
     */
    public function getQ3LengthString()
    {
        return number_format($this->getQuarterLength(3), 2, ',', '.').'m';
    }

    /**
     * @return string
     */
    public function getQ4LengthString()
    {
        return number_format($this->getQuarterLength(4), 2, ',', '.').'m';
    }

    /**
     * @param int $quarter
     *
     * @return float
     */
    private function getQuarterLength($quarter)
    {
        return ($this->getLength() / 4) * $quarter;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getModel() ? $this->getModel().' ('.$this->getLength().'m)' : '---';
    }

    /**
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param string $material
     *
     * @return Blade
     */
    public function setMaterial(string $material): Blade
    {
        $this->material = $material;

        return $this;
    }
}
