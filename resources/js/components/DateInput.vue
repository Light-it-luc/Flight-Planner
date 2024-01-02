<script>
    export default {
        data() {
            return { selectedDate: this.date }
        },

        props: {
            title: String,
            inputType: String,
            startDate: String,
            endDate: String,
            date: String
        },

        watch: {
            date(newValue) {
                this.selectedDate = newValue
            }
        }
    }
</script>

<template>
    <div class="flex flex-col m-4 w-56">
        <label class="mb-2 pl-4 font-semibold text-gray-800">{{ title }}</label>
        <input
            class="min-w-56 text-ellipsis border border-gray-300 rounded-md"
            :type="inputType"
            v-model="selectedDate"
            :min="startDate ? startDate : null"
            :max="endDate ? endDate : null"
            @change="$emit('update:date', selectedDate)">
    </div>
</template>
