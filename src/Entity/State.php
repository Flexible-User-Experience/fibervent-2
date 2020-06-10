<?php

namespace App\Entity;

use App\Enum\AuditLanguageEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Intl\Intl;

/**
 * State.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\StateRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
class State extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2)
     */
    private $countryCode;

    /**
     * @var string
     */
    private $countryName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="state")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $customers;

    /**
     * Methods.
     */

    /**
     * State constructor.
     */
    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return State
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return Intl::getRegionBundle()->getCountryName($this->getCountryCode(), strtoupper(AuditLanguageEnum::DEFAULT_LANGUAGE_STRING));
    }

    /**
     * @param string $countryName
     *
     * @return $this
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @param ArrayCollection $customers
     *
     * @return State
     */
    public function setCustomers($customers)
    {
        $this->customers = $customers;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? $this->getName().' ('.$this->getCountryName().')' : '---';
    }
}
