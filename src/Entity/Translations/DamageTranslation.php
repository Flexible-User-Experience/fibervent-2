<?php

namespace App\Entity\Translations;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Class DamageTranslation.
 *
 * @category Translation
 *
 * @ORM\Entity
 * @ORM\Table(name="damage_translation",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="lookup_damage_unique_idx", columns={
 *     "locale", "object_id", "field"
 *   })}
 * )
 * ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class DamageTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Damage", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     * ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    protected $object;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? (string) $this->getId() : '---';
    }
}
