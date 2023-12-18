<script>
    export default {

        props: {
            title: String,
            cities: Array,
            selectedCityId: Number,
            updateCity: Function
        }
    }
</script>

<template>
    <div class="flex flex-col mx-4 mb-8 w-42">
        <h3 class="mb-2 pl-4 font-semibold text-gray-800">{{ title }}</h3>
        <select
            class="w-full text-ellipsis rounded-md"
            v-model="selectedCityId"
            @change="updateCity(selectedCityId)"
        >

            <option
                v-for="city in cities"
                :key="city.id"
                :value="city.id"

            >{{ city.name }}</option>

        </select>

    </div>
</template>
