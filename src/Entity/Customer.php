<?php

namespace App\Entity;

use App\Entity\Traits\AddressTrait;
use App\Entity\Traits\CityTrait;
use App\Entity\Traits\CodeTrait;
use App\Entity\Traits\ImageTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PostalCodeTrait;
use App\Entity\Traits\StateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Customer.
 *
 * @category Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 * @Vich\Uploadable
 */
class Customer extends AbstractBase
{
    use NameTrait;
    use AddressTrait;
    use PostalCodeTrait;
    use StateTrait;
    use CityTrait;
    use CodeTrait;
    use ImageTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(strict=true, checkMX=true, checkHost=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS=true)
     */
    private $web;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="customer", fileNameProperty="imageName")
     * @Assert\File(
     *     maxSize = "10M",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth = 250)
     */
    private $imageFile;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="customers")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $state;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Windfarm", mappedBy="customer", cascade={"persist", "remove"})
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $windfarms;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="customer", cascade={"persist", "remove"})
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $contacts;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $showLogoInPdfs = false;

    /**
     * Methods.
     */

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        $this->windfarms = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $web
     *
     * @return Customer
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getWindfarms()
    {
        return $this->windfarms;
    }

    /**
     * @param ArrayCollection $windfarms
     *
     * @return Customer
     */
    public function setWindfarms($windfarms)
    {
        $this->windfarms = $windfarms;

        return $this;
    }

    /**
     * @param Windfarm $windfarm
     *
     * @return $this
     */
    public function addWindfarm(Windfarm $windfarm)
    {
        $windfarm->setCustomer($this);
        $this->windfarms->add($windfarm);

        return $this;
    }

    /**
     * @param Windfarm $windfarm
     *
     * @return $this
     */
    public function removeWindfarm(Windfarm $windfarm)
    {
        $windfarm->setCustomer(null);
        $this->windfarms->removeElement($windfarm);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param ArrayCollection $contacts
     *
     * @return Customer
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * @param User $contact
     *
     * @return $this
     */
    public function addContact(User $contact)
    {
        $contact->setCustomer($this);
        $this->contacts->add($contact);

        return $this;
    }

    /**
     * @param User $contact
     *
     * @return $this
     */
    public function removeContact(User $contact)
    {
        $contact->setCustomer(null);
        $this->contacts->removeElement($contact);

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowLogoInPdfs()
    {
        return $this->showLogoInPdfs;
    }

    /**
     * @return bool
     */
    public function getShowLogoInPdfs()
    {
        return $this->showLogoInPdfs;
    }

    /**
     * @param bool $showLogoInPdfs
     *
     * @return $this
     */
    public function setShowLogoInPdfs($showLogoInPdfs)
    {
        $this->showLogoInPdfs = $showLogoInPdfs;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? $this->getName() : '---';
    }
}
