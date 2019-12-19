<?php

namespace App\Entity\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * GpsCoordinates trait.
 *
 * @category Trait
 */
trait GpsCoordinatesTrait
{
    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=20, nullable=true, name="gps_longitude")
     */
    private $longitude = 0.716726;

    /**
     * @var float
     *
     * @ORM\Column(type="float", precision=20, nullable=true, name="gps_latitude")
     */
    private $latitude = 40.881604;

    /**
     * @var float
     */
    private $gpsLongitude = 0.716726;

    /**
     * @var float
     */
    private $gpsLatitude = 40.881604;

    /**
     * Methods.
     */

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     *
     * @return $this
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     *
     * @return $this
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getGpsLongitude()
    {
        return $this->getLongitude();
    }

    /**
     * @param float $gpsLongitude
     *
     * @return $this
     */
    public function setGpsLongitude($gpsLongitude)
    {
        return $this->setLongitude($gpsLongitude);
    }

    /**
     * @return float
     */
    public function getGpsLatitude()
    {
        return $this->getLatitude();
    }

    /**
     * @param float $gpsLatitude
     *
     * @return $this
     */
    public function setGpsLatitude($gpsLatitude)
    {
        return $this->setLatitude($gpsLatitude);
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
            'latitude' => $this->getGpsLatitude(),
            'longitude' => $this->getGpsLongitude(),
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
        $this->setGpsLatitude($latlng['latitude']);
        $this->setGpsLongitude($latlng['longitude']);

        return $this;
    }
}
