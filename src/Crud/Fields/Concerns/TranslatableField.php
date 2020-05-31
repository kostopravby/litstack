<?php

namespace Fjord\Crud\Fields\Concerns;

trait TranslatableField
{
    /**
     * Set field translatable.
     *
     * @param boolean $translatable
     * @return self
     */
    public function translatable(bool $translatable = true)
    {
        $this->setAttribute($translatable);

        return $this;
    }

    /**
     * Set translatable default attribute
     *
     * @return boolean
     */
    public function setTranslatableDefault(): bool
    {
        return is_attribute_translatable($this->id, $this->model);
    }
}
