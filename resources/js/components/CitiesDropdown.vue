<script>
    export default {
        data() {
            return {
                selectedCityId: 0
            }
        },

        props: {
            cities: Array,
            origin: Boolean,
            disabledCity: Number,
            title: String
        },

        methods: {
            handleSelectChange() {
                const eventData = {
                    origin: this.origin,
                    selectedCityId: this.selectedCityId
                }

                this.$emit('changeCity', eventData)
            }
        },

        computed: {
            availableCities() {
                return this.cities.filter(city => city.id !== this.disabledCity || city.id === 0 )
            }
        }
    }
</script>

<template>
    <div class="flex flex-col mx-4 mb-8 w-42">
        <h3 class="mb-2 pl-4 font-semibold text-gray-800">{{ title }}</h3>
        <select
            class="w-full text-ellipsis rounded-md select2"
            v-model="selectedCityId"
            @change="handleSelectChange"
        >

            <option
                v-for="city in availableCities"
                :key="city.id"
                :value="city.id"

            >{{ city.name }}</option>

        </select>

    </div>
</template>
