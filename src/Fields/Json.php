<?php

namespace Wame\LaravelNovaJsonField\Fields;

use Laravel\Nova\Exceptions\NovaException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\SupportsDependentFields;

class Json extends Field
{
    use SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'laravel-nova-json-field';

    public function __construct($name, $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->dependentShouldEmitChangesEvent = true;

        $this->showOnIndex = false;

        $this->withMeta([
            'fields' => [],
        ]);
    }

    public function showOnIndex($callback = true)
    {
        throw NovaException::helperNotSupported(__FUNCTION__, static::class);
    }

    public function fields(?array $fields): self
    {
        return $this->withMeta(['fields' => $fields ?? []]);
    }

    public function setFields(?array $fields): void
    {
        $this->withMeta(['fields' => $fields ?? []]);
    }
}
