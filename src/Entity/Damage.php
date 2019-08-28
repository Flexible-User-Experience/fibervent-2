<?php

namespace App\Entity;

use App\Entity\Traits\CodeTrait;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\TranslationsTrait;
use App\Entity\Translations\DamageTranslation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Damage.
 *
 * @category Entity
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\DamageRepository")
 * @UniqueEntity("code")
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="AppBundle\Entity\Translations\DamageTranslation")
 */
class Damage extends AbstractBase
{
    use CodeTrait;
    use DescriptionTrait;
    use TranslationsTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $section;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=45, unique=true)
     */
    private $code;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Translations\DamageTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     * @Assert\Valid(deep = true)
     *
     * @var ArrayCollection
     */
    private $translations;

    /**
     * Methods.
     */

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param int $section
     *
     * @return Damage
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Damage
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param DamageTranslation $translation
     *
     * @return $this
     */
    public function addTranslation(DamageTranslation $translation)
    {
        if ($translation->getContent()) {
            $translation->setObject($this);
            $this->translations[] = $translation;
        }

        return $this;
    }

    /**
     * @param DamageTranslation $translation
     *
     * @return $this
     */
    public function removeTranslation(DamageTranslation $translation)
    {
        $this->translations->removeElement($translation);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode() ? $this->getCode().' · '.$this->getDescription() : '---';
    }
}
