<script>
    import ObjectsDropdown from './ObjectsDropdown.vue';
    import DateInput from './DateInput.vue';

    export default {
        components: { ObjectsDropdown, DateInput },

        data() {
            return {
                originCityId: 0,
                destinationCityId: 0,
                airlineId: 0,
                departure: null,
                arrival: null,
            }
        },

        props: {
            airlines: Array,
            cities: Array,
            queryParams: Object,
        },

        computed: {
            allowedOrigins() {
                const allCities = { id: 0, name: "All Cities" }

                if (this.airlineId) {
                    const selectedAirline = this.airlines.find(airline => airline.id === this.airlineId)
                    const filteredCities = selectedAirline.cities
                        .filter(city => city.id !== this.destinationCityId)
                        .sort((a, b) => a.name.localeCompare(b.name))

                    return [allCities, ...filteredCities]
                }

                return [allCities, ...this.cities.filter(city => city.id !== this.destinationCityId)]
            },

            allowedDestinations() {
                const allCities = { id: 0, name: "All Cities" }

                if (this.airlineId) {
                    const selectedAirline = this.airlines.find(airline => airline.id === this.airlineId)
                    const filteredCities = selectedAirline.cities
                        .filter(city => city.id !== this.originCityId)
                        .sort((a, b) => a.name.localeCompare(b.name))

                    return [allCities, ...filteredCities]
                }

                return [allCities, ...this.cities.filter(city => city.id !== this.originCityId)]
            },

            allAirlines() {
                const all = { id: 0, name:"All Airlines" }
                return [all, ...this.airlines]
            }
        },

        methods: {
            applyFilters() {
                const filters = {
                    origin: this.originCityId,
                    destination: this.destinationCityId,
                    airline: this.airlineId,
                    departure: this.departure,
                    arrival: this.arrival,
                }

                const params = new URLSearchParams(window.location.search)

                for (const [key, value] of Object.entries(filters)) {
                    if (value) {
                        params.set(key, value)
                    } else {
                        params.delete(key)
                    }
                }
                this.$emit("update:queryParams", params)
            },

            resetCities(newAirlineId) {
                this.airlineId = newAirlineId
                this.originCityId = 0
                this.destinationCityId = 0
            }
        }
    }
</script>

<template>
    <div class="flex flex-row mx-20">
        <objects-dropdown
            title="Airlines"
            :objects="allAirlines"
            v-model:selectedId="airlineId"
            @update:selectedId="resetCities"
        ></objects-dropdown>

        <objects-dropdown
            title="Origin"
            :objects="allowedOrigins"
            v-model:selectedId="originCityId"
        ></objects-dropdown>

        <objects-dropdown
            title="Destination"
            :objects="allowedDestinations"
            v-model:selectedId="destinationCityId"
        ></objects-dropdown>
    </div>

    <div class="flex flex-row mx-20 mb-8">
        <date-input
            title="Departure"
            inputType="date"
            :endDate="arrival"
            v-model:date="departure"
        ></date-input>

        <date-input
            title="Arrival"
            :inputType="'date'"
            :startDate="departure"
            v-model:date="arrival"
        ></date-input>

        <div class="ml-6 mt-12">
            <button
                id="filter-button"
                class="font-semibold text-white dark:bg-gray-500 hover:bg-gray-400
                w-32 py-1 px-2 rounded-md"
                @click="applyFilters"
            >Apply Filters</button>
        </div>
    </div>

</template>
