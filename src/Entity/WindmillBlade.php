<?php

namespace App\Entity;

use App\Entity\Traits\CodeTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * WindmillBlade.
 *
 * @category Entity
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\WindmillBladeRepository")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class WindmillBlade extends AbstractBase
{
    use CodeTrait;

    /**
     * @var string serial number
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @var int windmill blade order (1, 2, 3)
     *
     * @ORM\Column(name="`order`", type="integer", options={"default"=0})
     */
    private $order;

    /**
     * @var Windmill
     *
     * @ORM\ManyToOne(targetEntity="Windmill", inversedBy="windmillBlades")
     */
    private $windmill;

    /**
     * Methods.
     */

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
     * @return WindmillBlade
     */
    public function setWindmill($windmill)
    {
        $this->windmill = $windmill;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return WindmillBlade
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getOrder() ? strval($this->getOrder()) : '---';
    }
}
