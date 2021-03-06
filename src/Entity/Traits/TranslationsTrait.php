<?php

namespace App\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Translations trait.
 *
 * @category Trait
 */
trait TranslationsTrait
{
    /**
     * @param ArrayCollection $translations
     *
     * @return $this
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}
