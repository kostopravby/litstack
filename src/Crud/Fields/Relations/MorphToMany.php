<?php

namespace Fjord\Crud\Fields\Relations;

use Fjord\Crud\Fields\Concerns\FormItemWrapper;

class MorphToMany extends ManyRelationField
{
    use FormItemWrapper;

    /**
     * Properties passed to Vue component.
     *
     * @var array
     */
    protected $props = [
        'type' => 'morphToMany'
    ];

    /**
     * Required attributes.
     *
     * @var array
     */
    public $requiredAttributes = [];

    /**
     * Available Field attributes.
     *
     * @var array
     */
    public $availableAttributes = [
        'form',
    ];

    /**
     * Default Field attributes.
     *
     * @var array
     */
    public $defaultAttributes = [];
}
