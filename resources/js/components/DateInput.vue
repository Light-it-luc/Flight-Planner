<script>
    export default {
        data() {
            return {
                selectedDate: null
            }
        },

        props: {
            title: String,
            inputType: String,
            startDate: String,
            endDate: String,
        }
    }
</script>

<template>
    <div class="flex flex-col m-4 w-42">
        <label class="mb-2 pl-4 font-semibold text-gray-800">{{ title }}</label>
        <input
            class="w-full text-ellipsis border border-gray-300 rounded-md"
            :type="inputType"
            v-model="selectedDate"
            :min="startDate ? startDate : null"
            :max="endDate ? endDate : null"
            @change="$emit('update:date', selectedDate)">
    </div>
</template>
