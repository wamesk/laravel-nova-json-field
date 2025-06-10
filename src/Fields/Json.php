<?php

namespace Wame\LaravelNovaJsonField\Fields;

use JsonException;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Exceptions\NovaException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;

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
            'repeatable' => false,
        ]);
    }

    /**
     * @throws JsonException
     */
    public function fill(NovaRequest $request, $model): void
    {
        $attribute = $this->attribute;

        $value = $request->input($attribute);

        $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        unset($model->$attribute);

        $model->$attribute = $value;
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

    public function repeatable(bool $repeatable = true): self
    {
        return $this->withMeta([
            'repeatable' => $repeatable,
        ]);
    }

    public function addButton(string $title): self
    {
        return $this->withMeta([
            'addButtonTitle' => $title,
        ]);
    }

    public function hideSectionName(): Json
    {
        return $this->withMeta([
            'hideSectionName' => true,
        ]);
    }
}
