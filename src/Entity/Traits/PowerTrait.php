<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PowerTrait.
 *
 * @category Trait
 */
trait PowerTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="float", precision=2, nullable=true)
     */
    protected $power;

    /**
     * @return float
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @param float $power
     *
     * @return $this
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }
}
