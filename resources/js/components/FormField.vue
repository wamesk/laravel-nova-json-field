<template>
    <div>
        <div v-for="parameterField in currentField.fields">
            <DefaultField :field="currentFieldFor(parameterField.name)" :fieldName="parameterField.label" :errors="errors">
                <template #field>
                    <template v-if="parameterField.type == 'select'">
                        <div class="flex relative w-full alternative-component-select-control">
                            <select
                                :id="parameterField.name"
                                v-model="values[parameterField.name]"
                                :required="parameterField.required"
                                class="w-full block form-control form-control-bordered form-input"
                            >
                                <option :disabled="parameterField.required" :value="undefined">{{ parameterField.name }}</option>
                                <option v-for="option in parameterField.options" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>

                            <svg class="shrink-0 pointer-events-none absolute text-gray-700 right-[11px] top-[15px] alternative-component-icon-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6"><path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path></svg>
                        </div>
                    </template>
                    <template v-else>
                        <input
                            :id="parameterField.name"
                            :type="parameterField.type"
                            class="w-full form-control form-input form-control-bordered"
                            :class="errorClasses"
                            v-model="values[parameterField.name]"
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
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'
import {isString} from "lodash";

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            values: {}
        };
    },

    methods: {
        /*
        * Set the initial, internal value for the field.
        */
        setInitialValue() {
            console.log('value', this.field.value)
            let value = this.field.value

            if (isString(value)) {
                value = JSON.parse(value)
            }

            this.values = value ?? {}

            this.fields = this.currentField.fields
        },

        currentFieldFor(name) {
            const new_field = JSON.parse(JSON.stringify(this.currentField))
            new_field.name = name

            let parameterField = this.currentField.fields.filter(field => field.name == name)[0]

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
            this.value = JSON.stringify(this.values);

            formData.append(this.currentField.attribute, this.value || '')
        },
    },
}
</script>
