<?php

namespace App\Entity\Traits;

use App\Entity\State;

/**
 * State trait.
 *
 * @category Trait
 */
trait StateTrait
{
    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param State $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }
}
