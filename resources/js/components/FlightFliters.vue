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
            cities: Array
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

        watch: {
            airlineId(newValue, oldValue) {
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
            selectBoxId="select-airline"
        ></objects-dropdown>

        <objects-dropdown
            title="Origin"
            :objects="allowedOrigins"
            v-model:selectedId="originCityId"
            selectBoxId="select-origin"
        ></objects-dropdown>

        <objects-dropdown
            title="Destination"
            :objects="allowedDestinations"
            v-model:selectedId="destinationCityId"
            selectBoxId="select-destination"
        ></objects-dropdown>
    </div>

    <div class="flex flex-row mx-20">
        <date-input
            title="Departure"
            :startDate="null"
            :endDate="arrival"
            v-model:date="departure"
            selectBoxId="select-departure"
        ></date-input>

        <date-input
            title="Arrival"
            :startDate="departure"
            :endDate="null"
            v-model:date="arrival"
            selectBoxId="select-arrival"
        ></date-input>

        <div class="ml-6 pt-8">
            <button
                id="filter-button"
                class="font-semibold text-white dark:bg-gray-500 hover:bg-gray-400
                w-32 py-1 px-2 rounded-md"
            >Apply Filters</button>
        </div>
    </div>

</template>
