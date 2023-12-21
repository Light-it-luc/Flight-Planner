<script>
    import ObjectsDropdown from './ObjectsDropdown.vue';
    import DateInput from './DateInput.vue';
    import Modal from './Modal.vue';
    import axios from 'axios';

    export default {

        components: { ObjectsDropdown, DateInput, Modal },

        data() {
            return {
                cities: [],
                airlines: [],
                originCityId: 0,
                destinationCityId: 0,
                airlineId: 0,
                departure: null,
                arrival: null,
            }
        },

        created() {
            axios.get("api/v1/cities?all=true")
            .then(res => this.cities = res.data)
            .catch(err => console.log(err))

            axios.get("api/v1/airlines?all=true")
            .then(res => this.airlines = res.data)
            .catch(err => console.log(err))
        },

        methods: {
            updateAirline(newAirlineId) {
                this.airlineId = newAirlineId
                this.originCityId = 0
                this.destinationCityId = 0
            },

            updateStartDate(date) {
                this.departure = date
            },

            updateEndDate(date) {
                this.arrival = date
            },
        },

        computed: {
            allowedOrigins() {
                const allCities = { id: 0, name: "All Cities" }

                if (this.airlineId) {
                    const selectedAirline = this.airlines.find(airline => airline.id === this.airlineId)
                    const filteredCities = selectedAirline.cities.filter(city => city.id !== this.destinationCityId)
                    return [allCities, ...filteredCities]
                }

                return [allCities, ...this.cities.filter(city => city.id !== this.destinationCityId)]
            },

            allowedDestinations() {
                const allCities = { id: 0, name: "All Cities" }

                if (this.airlineId) {
                    const selectedAirline = this.airlines.find(airline => airline.id === this.airlineId)
                    const filteredCities = selectedAirline.cities.filter(city => city.id !== this.originCityId)
                    return [allCities, ...filteredCities]
                }

                return [allCities, ...this.cities.filter(city => city.id !== this.originCityId)]
            },

            allAirlines() {
                const all = { id: 0, name:"All Airlines" }
                return [all, ...this.airlines]
            }
        }
    }
</script>

<template>

    <modal
        :airlines="airlines"
        :cities="cities"
    ></modal>

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
            :updateDate="updateStartDate"
            selectBoxId="select-departure"
        ></date-input>

        <date-input
            title="Arrival"
            :startDate="departure"
            :endDate="null"
            :updateDate="updateEndDate"
            selectBoxId="select-arrival"
        ></date-input>

        <div class="ml-6 pt-8">
            <button
                id="filter-button"
                class="font-semibold text-white dark:bg-gray-500 hover:bg-gray-400
                w-20 py-1 px-2 rounded-md"
            >Filter</button>

            <button
                id="create-button"
                class="ml-4 font-semibold text-white dark:bg-gray-500 hover:bg-gray-400
                w-20 py-1 px-2 rounded-md"
            >Create</button>
        </div>
    </div>

</template>
