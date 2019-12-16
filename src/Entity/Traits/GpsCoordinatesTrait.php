<?php

namespace App\Entity\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * GpsCoordinates trait.
 *
 * @category Trait
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 */
trait GpsCoordinatesTrait
{
    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=20, nullable=true)
     */
    private $gpsLongitude = 0.716726;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=20, nullable=true)
     */
    private $gpsLatitude = 40.881604;

    /**
     * Methods.
     */

    /**
     * @return float
     */
    public function getGpsLongitude()
    {
        return $this->gpsLongitude;
    }

    /**
     * @param float $gpsLongitude
     *
     * @return $this
     */
    public function setGpsLongitude($gpsLongitude)
    {
        $this->gpsLongitude = $gpsLongitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getGpsLatitude()
    {
        return $this->gpsLatitude;
    }

    /**
     * @param float $gpsLatitude
     *
     * @return $this
     */
    public function setGpsLatitude($gpsLatitude)
    {
        $this->gpsLatitude = $gpsLatitude;

        return $this;
    }

    /**
     * Get LatLng.
     *
     * @Assert\NotBlank()
     * @OhAssert\LatLng()
     *
     * @return array
     */
    public function getLatLng()
    {
        return array(
            'lat' => $this->getGpsLatitude(),
            'lng' => $this->getGpsLongitude(),
        );
    }

    /**
     * Set LatLng.
     *
     * @param array $latlng
     *
     * @return $this
     */
    public function setLatLng($latlng)
    {
        $this->setGpsLatitude($latlng['lat']);
        $this->setGpsLongitude($latlng['lng']);

        return $this;
    }
}
