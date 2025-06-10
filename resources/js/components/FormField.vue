<template>
    <div v-if="loaded && fields.length > 0">
        <div v-for="(section, sectionIndex) in sections">
            <div v-if="section.name" class="flex flex-col md:flex-row alternative-component-field-wrapper alternative-component-form-heading-field alternative-field-asdads alternative-resource-devices" errors="[object Object]">
                <div class="remove-last-margin-bottom leading-normal w-full py-4 px-8 section-header">
                    <h3 class="uppercase tracking-wide font-bold text-xs alternative-component-heading" dusk="heading">
                        {{ field.hideSectionName ? '' : section.name }}
                    </h3>
                    <svg v-if="field.repeatable" @click="removeSection(sectionIndex)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="25px" style="cursor: pointer">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
            </div>
            <div v-for="parameterField in section.fields">
                <DefaultField :field="currentFieldFor(parameterField)" :fieldName="parameterField.label" :errors="errors">
                    <template #field>
                        <template v-if="parameterField.type == 'select'">
                            <div class="flex relative w-full alternative-component-select-control">
                                <select
                                    :id="parameterField.name"
                                    v-model="value[section.id || sectionIndex][parameterField.id]"
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
                        <template v-else-if="parameterField.type === 'checkbox'">
                            <input
                                type="checkbox"
                                class="checkbox"
                                v-model="value[section.id || sectionIndex][parameterField.id]"
                                @click.stop
                            />
                        </template>
                        <template v-else>
                            <input
                                :id="parameterField.name"
                                :type="parameterField.type"
                                class="w-full form-control form-input form-control-bordered"
                                :class="errorClasses"
                                v-model="value[section.id || sectionIndex][parameterField.id]"
                                :required="parameterField.required"
                                :placeholder="parameterField.placeholder"
                            />
                        </template>
                    </template>
                </DefaultField>
            </div>
        </div>

        <div class="text-center">
            <p v-if="hasError" class="my-2 text-red-500 mb-2">
                {{ firstError }}
            </p>

            <button
                v-if="field.repeatable && fields.length > 0"
                type="button"
                class="border text-left appearance-none cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 relative disabled:cursor-not-allowed inline-flex items-center justify-center shadow h-9 px-3 bg-primary-500 border-primary-500 hover:[&amp;:not(:disabled)]:bg-primary-400 hover:[&amp;:not(:disabled)]:border-primary-400 text-white dark:text-gray-900"
                @click="addSection"
            >
                <span class="flex items-center gap-1">
                    {{ field.addButtonTitle ?? 'Pridať položku' }}
                </span>
            </button>
        </div>
    </div>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'
import {isString} from "lodash";
import {toRaw} from "vue";

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            loaded: false,
            repeatCount: 1,
            sections: [],
            value: {},
        };
    },

    watch: {
        fields(oldFields, newFields) {
            const noChange = JSON.stringify(oldFields) === JSON.stringify(newFields);
            if (noChange) {
                return;
            }

            this.value = {};
            this.sections = [];

            if (!this.currentField.repeatable) {
                this.getSections();
            }
        },
    },

    computed: {
        fields() {
            return this.currentField.fields;
        },
    },

    methods: {
        getSections() {
            let sections = [];

            if (this.currentField.asModels) {
                for (let i = 0; i < this.currentField.fields.length; i++) {
                    const model = this.currentField.fields[i]

                    sections = this.createSection(sections, model.id, model.name, model.fields);
                }
            }

            if (!this.currentField.asModels) {
                if (this.currentField.repeatable) {
                    for (let j = 0; j < this.repeatCount; j++) {
                        sections = this.createSection(sections, j, j + 1);
                    }
                }

                if (!this.currentField.repeatable) {
                    sections = this.createSection(sections, 0);
                }
            }

            this.sections = sections;
            this.loaded = true;
        },

        createSection(sections, sectionId, sectionName, fields) {
            fields = fields || this.currentField.fields;

            let value = {};

            if (this.currentField.value !== undefined) {
                value = this.currentField.value;

                if (this.currentField.asModels) {
                    value = value[sectionId]?.values || {}
                }
                else if (this.currentField.repeatable) {
                    console.log('value', value)
                    if (value === null) {
                        value = [{}];
                    }
                    console.log('value', value)
                    value = value[0] || {};
                }
            }

            if (value === null) {
                value = {};
            }

            for (let i = 0; i < fields.length; i++) {
                if (value[fields[i].id] === undefined) {
                    value[fields[i].id] = null;
                }
            }

            console.log('fields', fields)

            this.value[sectionId] = value

            let any = {
                id: sectionId,
                name: sectionName,
                fields: fields,
            };
            sections.push(any);

            return sections;
        },

        addSection() {
            this.sections = this.createSection(this.sections, this.sections.length, this.sections.length + 1);
        },

        removeSection(sectionIndex) {
            this.sections.splice(sectionIndex, 1);
            delete this.value[sectionIndex]
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

            this.getSections();
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

            new_field.validationKey = new_field.validationKey + '_' + parameterField.id;

            return new_field
        },

        /**
        * Fill the given FormData object with the field's internal value.
        */
        fill(formData) {
            let value = this.value;

            if (!this.field.asModels && !this.field.repeatable) {
                value = value[0];
            }

            value = JSON.stringify(value);

            console.log('stringify value', value)

            formData.append(this.currentField.attribute, value || null)
        },
    },
}
</script>
<style scoped>
  .section-header {
    display: flex;
    align-items: center;
    justify-content: space-around;
  }
</style>