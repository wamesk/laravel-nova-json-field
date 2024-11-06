<template>
    <div v-for="section in sections">
        <div v-if="section.name" class="flex flex-col md:flex-row alternative-component-field-wrapper alternative-component-form-heading-field alternative-field-asdads alternative-resource-devices" errors="[object Object]">
            <div class="remove-last-margin-bottom leading-normal w-full py-4 px-8">
                <h3 class="uppercase tracking-wide font-bold text-xs alternative-component-heading" dusk="heading">
                    {{ section.name }}
                </h3>
            </div>
        </div>
        <PanelItem :index="index" :field="currentFieldFor(field, section?.id)" v-for="field in section.fields" />
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
                this.sections.push({
                    id: this.currentField.fields[i].id,
                    name: this.currentField.fields[i].name,
                    fields: this.currentField.fields[i].fields,
                })
            }
        }

        else {
            this.sections.push({
                fields: this.currentField.fields
            })
        }
    },

    methods: {
        currentFieldFor(field, sectionId) {
            const new_field = JSON.parse(JSON.stringify(this.currentField))
            new_field.name = field.name

            if (field['placeholder']) {
                new_field.placeholder = field['placeholder']
            }

            let value = this.value

            if (isString(value)) {
                value = JSON.parse(value)
            }

            if (!this.currentField.asModels) {
                new_field.value = value[field.name]
            }

            if (this.currentField.asModels) {
                value = JSON.parse(value[sectionId])[field.name]

                new_field.value = value
            }

            return new_field
        }
    }
}
</script>
