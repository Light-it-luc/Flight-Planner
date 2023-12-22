<script>
    import Multiselect from '@vueform/multiselect'

    export default {
        components: { Multiselect },

        data() {
            return {
                selectedOption: this.selectedId
            }
        },

        props: {
            title: String,
            objects: Array,
            selectBoxId: String,
            selectedId: Number
        },

        methods: {
            handleSelectChange() {
                this.$emit('update:selectedId', this.selectedOption)
            },

            handleClearSelect() {
                this.selectedOption = 0
                this.handleSelectChange()
            }
        },

        watch: {
            selectedId(newValue) {
                this.selectedOption = newValue
            }
        }
    }
</script>

<template>
    <div :id="`select-${title.toLowerCase()}`" class="flex flex-col mx-4 mb-4 w-56">
        <h3 class="mb-2 pl-4 font-semibold text-gray-800">{{ title }}</h3>
        <multiselect
            v-model="selectedOption"
            :options="objects"
            :searchable="true"
            label="name"
            valueProp="id"
            @select="handleSelectChange"
            @clear="handleClearSelect"
        />
    </div>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>
