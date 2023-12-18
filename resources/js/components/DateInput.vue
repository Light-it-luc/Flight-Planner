<script>
    export default {
        data() {
            return {
                selectedDate: null
            }
        },

        props: {
            title: String,
            startDate: String | null,
            endDate: String | null,
            updateDate: Function
        },

        methods: {
            dateToString(date) {
                return date.toISOString().split("T")[0]
            }
        }
    }
</script>

<template>
    <div class="flex flex-col mx-4 mb-8 w-42">
        <label class="mb-2 pl-4 font-semibold text-gray-800" for="start">{{ title }}</label>
        <input
            class="w-full text-ellipsis rounded-md"
            type="date"
            v-model="selectedDate"
            :min="startDate"
            :max="endDate"
            @change="updateDate(selectedDate)">
    </div>
</template>
