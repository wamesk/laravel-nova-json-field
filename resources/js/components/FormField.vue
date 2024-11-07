<template>
    <div>
        <div v-for="(section, sectionIndex) in sections">
            <div v-if="section.name" class="flex flex-col md:flex-row alternative-component-field-wrapper alternative-component-form-heading-field alternative-field-asdads alternative-resource-devices" errors="[object Object]">
                <div class="remove-last-margin-bottom leading-normal w-full py-4 px-8">
                    <h3 class="uppercase tracking-wide font-bold text-xs alternative-component-heading" dusk="heading">
                        {{ section.name }}
                    </h3>
                </div>
            </div>
            <div v-for="parameterField in section.fields">
                <DefaultField :field="currentFieldFor(parameterField)" :fieldName="parameterField.label" :errors="errors">
                    <template #field>
                        <template v-if="parameterField.type == 'select'">
                            <div class="flex relative w-full alternative-component-select-control">
                                <select
                                    :id="parameterField.name"
                                    v-model="parameterField.value"
                                    :required="parameterField.required"
                                    class="w-full block form-control form-control-bordered form-input"
                                >
                                    <option :disabled="parameterField.required" :value="null">{{ parameterField.name }}</option>
                                    <option v-for="option in parameterField.options" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>

                                <svg class="shrink-0 pointer-events-none absolute text-gray-700 right-[11px] top-[15px] alternative-component-icon-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6"><path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path></svg>
                            </div>
                        </template>
                        <template v-if="parameterField.type === 'checkbox'">
                            <input
                                type="checkbox"
                                class="checkbox"
                                :disabled="disabled"
                                :checked="checked"
                                v-model="parameterField.value"
                                @change="handleChange"
                                @click.stop
                            />
                        </template>
                        <template v-else>
                            <input
                                :id="parameterField.name"
                                :type="parameterField.type"
                                class="w-full form-control form-input form-control-bordered"
                                :class="errorClasses"
                                v-model="parameterField.value"
                                :required="parameterField.required"
                                :placeholder="parameterField.placeholder"
                            />
                        </template>

                        <p v-if="hasError" class="my-2 text-danger">
                            {{ firstError }}
                        </p>
                    </template>
                </DefaultField>
            </div>
        </div>
    </div>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'
import {isString} from "lodash";

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            values: [],
        };
    },

    computed: {
        value() {
            return this.getValues()
        },
        sections() {
            console.log('this.currentField.value', this.currentField.value)
            let originalValues = this.currentField.value;

            if (isString(originalValues)) {
                originalValues = JSON.parse(originalValues)
            }

            let sections = [];

            if (this.currentField.asModels) {
                for (let i = 0; i < this.currentField.fields.length; i++) {
                    let model = this.currentField.fields[i]

                    for (let j = 0; j < model.fields.length; j++) {
                        model.fields[j].value = ''
                        if (model.fields[j].type === 'select') {
                            model.fields[j].value = null
                        }
                        if (model.fields[j].type === 'checkbox') {
                            model.fields[j].value = false
                        }

                        let modelValue = originalValues?.[model.id];

                        console.log('model', model)
                        console.log('modelValue', modelValue)
                        console.log('originalValues', originalValues)

                        let value = undefined

                        if (isString(modelValue)) {
                            value = JSON.parse(modelValue)?.[model.fields[j].id]
                        }

                        if (value !== undefined) {
                            model.fields[j].value = value
                        }
                    }

                    sections.push({
                        id: model.id,
                        name: model.name,
                        fields: model.fields,
                    })
                }
                return sections
            }

            let fields = this.currentField.fields

            for (let i = 0; i < fields.length; i++) {
                fields[i].value = ''
                if (fields[i].type === 'select') {
                    fields[i].value = null
                }
                if (fields[i].type === 'checkbox') {
                    fields[i].value = false
                }

                if (originalValues !== null && originalValues[fields[i].id] !== undefined) {
                    fields[i].value = originalValues[fields[i].id]
                }
            }

            sections.push({fields: this.currentField.fields})

            return sections
        }
    },

    methods: {
        getValues() {
            let data = []

            for (let i = 0; i < this.sections.length; i++) {
                let section = this.sections[i];

                data[i] = {
                    sectionId: section.id,
                    sectionName: section.name,
                }
                for (let j = 0; j < section.fields.length; j++) {
                    let field = section.fields[j]

                    data[i][field.id] = field.value
                }
            }

            if (!this.currentField.asModels) {
                data = data[0]
            }

            return data
        },

        /*
        * Set the initial, internal value for the field.
        */
        setInitialValue() {
            let value = this.field.value

            if (isString(value)) {
                value = JSON.parse(value)
            }

            this.values = value ?? []

            this.fields = this.currentField.fields
        },

        currentFieldFor(parameterField) {
            const new_field = JSON.parse(JSON.stringify(this.currentField))
            new_field.name = parameterField.name

            if (parameterField['placeholder']) {
                new_field.placeholder = parameterField['placeholder']
            }

            if (parameterField['required']) {
                new_field.required = parameterField['required']
            }

            return new_field
        },

        /**
        * Fill the given FormData object with the field's internal value.
        */
        fill(formData) {
            this.value = JSON.stringify(this.getValues());

            formData.append(this.currentField.attribute, this.value || null)
        },
    },
}
</script>
