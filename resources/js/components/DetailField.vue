<template>
    <PanelItem :index="index" :field="currentFieldFor(field.name)" v-for="field in field.fields" />
</template>
<script>
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'
import {isString} from "lodash";

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            values: {}
        };
    },

    mounted() {
        this.values = this.field.value
        console.log('this.values', this.values)
        console.log('this.field.fields', this.field.fields)
    },

    methods: {
        currentFieldFor(name) {
            const new_field = JSON.parse(JSON.stringify(this.currentField))
            new_field.name = name

            let userField = this.field.fields.filter(field => field.name == name)[0]

            if (userField['placeholder']) {
                new_field.placeholder = userField['placeholder']
            }

            let value = this.value

            if (isString(value)) {
                value = JSON.parse(value)
            }

            new_field.value = value[name]

            return new_field
        }
    }
}
</script>
