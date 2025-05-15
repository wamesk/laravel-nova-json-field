<template>
    <div v-for="section in sections">
        <div v-if="section.name" class="flex flex-col md:flex-row alternative-component-field-wrapper alternative-component-form-heading-field alternative-field-asdads alternative-resource-devices" errors="[object Object]">
            <div class="remove-last-margin-bottom leading-normal w-full py-4 px-8">
                <h3 class="uppercase tracking-wide font-bold text-xs alternative-component-heading" dusk="heading">
                    {{ section.name }}
                </h3>
            </div>
        </div>

        <div v-for="field in section.fields">
            <div class="flex flex-col -mx-6 px-6 py-2 space-y-2 md:flex-row @sm/peekable:flex-row @md/modal:flex-row md:py-0 @sm/peekable:py-0 @md/modal:py-0 md:space-y-0 @sm/peekable:space-y-0 @md/modal:space-y-0 alternative-component-panel-item alternative-field-description alternative-component-detail-text-field alternative-resource-devices alternative-component-keep-alive" dusk="description"><div class="md:w-1/4 @sm/peekable:w-1/4 @md/modal:w-1/4 md:py-3 @sm/peekable:py-3 @md/modal:py-3"><h4 class="font-normal @sm/peekable:break-all">
                <span>
                    {{ field.name }}
                </span>
            </h4>
            </div>
                <div class="break-all md:w-3/4 @sm/peekable:w-3/4 @md/modal:w-3/4 md:py-3 @sm/peekable:py-3 md/modal:py-3 lg:break-words @md/peekable:break-words @lg/modal:break-words">
                    <svg v-if="field.type === 'checkbox' && field.value === true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block text-green-500 alternative-component-heroicons-outline-check-circle alternative-component-icon" role="presentation"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <svg v-else-if="field.type === 'checkbox'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block text-red-500 alternative-component-heroicons-outline-x-circle alternative-component-icon" role="presentation"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p v-else class="flex items-center">
                        {{ field.value }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'
import {isString} from "lodash";

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            values: {},
            sections: [],
        };
    },

    mounted() {
        this.values = this.field.value

        if (this.currentField.asModels) {
            for (let i = 0; i < this.currentField.fields.length; i++) {
                const section = this.currentField.fields[i];

                this.sections.push({
                    id: section.id,
                    name: section.name,
                    fields: this.getFields(section.fields, section.id),
                })
            }
        }
        else {
            this.sections.push({
                fields: this.getFields(this.currentField.fields)
            })
        }
    },

    methods: {
        getFields(fields, sectionId) {
            for (let i = 0; i < fields.length; i++) {
                fields[i].value = this.getValue(fields[i].id, sectionId);
            }

            return fields;
        },
        getValue(fieldId, sectionId) {
            if (this.currentField.asModels) {
                return this.values[sectionId].values[fieldId] || null;
            }

            return this.values[fieldId] || null;
        }
    }
}
</script>
