<script>
    export default {
        data() {
            return {
                selectedAirlineId: 0
            }
        },

        props: {
            airlines: Array,
        }
    }
</script>

<template>
    <div class="flex flex-col mx-4 mb-12 w-48">
        <h3 class="mb-2 pl-4 font-semibold text-gray-800">Airlines</h3>
        <select
            class="w-full text-ellipsis rounded-md"
            v-model="selectedAirlineId"
        >

            <option
                v-for="airline in airlines"
                :key="airline.id"
                :value="airline.id"
            >{{ airline.name }}</option>

        </select>
    </div>
</template>
