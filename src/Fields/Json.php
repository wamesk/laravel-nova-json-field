<?php

namespace Wame\LaravelNovaJsonField\Fields;

use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Exceptions\NovaException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;
use Laravel\Nova\Fields\SupportsDependentFields;

class Json extends Field
{
    use SupportsDependentFields;

    /**
     * The resource class for the json field.
     *
     * @var $resourceClass ?string
     */
    public ?string $resourceClass;

    /**
     * The resource name for the json field.
     *
     * @var $resourceName ?string
     */
    public ?string $resourceName;

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
            'asModels' => false,
        ]);
    }

    /**
     * @throws HelperNotSupported
     */
    public function showOnIndex($callback = true)
    {
        throw NovaException::helperNotSupported(__FUNCTION__, static::class);
    }

    private function setData(?array $fields, bool $asModels = false): self
    {
        return $this->withMeta([
            'fields' => $fields ?? [],
            'asModels' => $asModels,
        ]);
    }

    public function fields(?array $fields): self
    {
        return $this->setData($fields);
    }

    public function models(?array $models): self
    {
        return $this->setData($models, true);
    }
}
