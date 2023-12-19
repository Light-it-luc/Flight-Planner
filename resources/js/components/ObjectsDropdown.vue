<script>
    export default {
        data() {
            return {
                selectedValue: this.selectedId
            }
        },

        props: {
            title: String,
            objects: Array,
            selectedId: Number,
            selectBoxId: String
        },

        methods: {
            handleSelectChange() {
                this.$emit('update:selectedId', this.selectedValue)
            }
        }
    }
</script>

<template>
    <div class="flex flex-col mx-4 mb-8">
        <h3 class="mb-2 pl-4 font-semibold text-gray-800">{{ title }}</h3>
        <select
            :id="selectBoxId"
            class="select2 w-32 h-160 text-ellipsis rounded-md"
            v-model="selectedValue"
            @change="handleSelectChange"
        >

            <option
                v-for="obj in objects"
                :key="obj.id"
                :value="obj.id"

            >{{ obj.name }}</option>

        </select>

    </div>
</template>
