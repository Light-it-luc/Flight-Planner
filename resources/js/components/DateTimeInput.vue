<script>
    export default {
        data() {
            return {
                selectedDateTime: null,
            }
        },

        props: {
            title: String,
            selectBoxId: String,
            startDateTime: String | null,
            endDateTime: String | null,
            date: String | null
        },

        methods: {
            handleDateTimeChange() {
                this.$emit('update:date', this.selectedDateTime)
            }
        },

        watch: {
            date(newValue) {
                this.selectedDateTime = newValue
            }
        }
    }
</script>

<template>
    <div class="flex flex-col mx-4 mb-8 w-56">
        <label class="mb-2 pl-4 font-semibold text-gray-800" for="start">{{ title }}</label>
        <input
            :id="selectBoxId"
            class="w-full text-ellipsis border border-gray-300 rounded-md"
            type="datetime-local"
            v-model="selectedDateTime"
            :min="startDateTime"
            :max="endDateTime"
            @change="handleDateTimeChange"
        >
    </div>
</template>
