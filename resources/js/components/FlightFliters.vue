<script>
    import CitiesDropdown from './CitiesDropdown.vue';
    import AirlinesDropdown from './AirlinesDropdown.vue';
    import DateInput from './DateInput.vue';
    import axios from 'axios';

    export default {

        components: { CitiesDropdown, AirlinesDropdown, DateInput },

        data() {
            return {
                cities: [],
                airlines: [],
                originCityId: 0,
                destinationCityId: 0,
                departure: null,
                arrival: null
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
            updateOrigin(newCityId) {
                this.originCityId = newCityId
            },

            updateDestination(newCityId) {
                this.destinationCityId = newCityId
            },

            updateStartDate(date) {
                this.departure = date
            },

            updateEndDate(date) {
                this.arrival = date
            }
        },

        computed: {
            allowedOrigins() {
                const allCities = { id: 0, name: "All Cities" }
                return [allCities, ...this.cities.filter(city => city.id !== this.destinationCityId)]
            },

            allowedDestinations() {
                const allCities = { id: 0, name: "All Cities" }
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

    <div class="flex flex-row mx-20">
            <cities-dropdown
                :title="'Origin'"
                :cities="allowedOrigins"
                :selectedCityId="originCityId"
                :updateCity="updateOrigin"
            ></cities-dropdown>

            <cities-dropdown
                :title="'Destination'"
                :cities="allowedDestinations"
                :selectedCityId="destinationCityId"
                :updateCity="updateDestination"
            ></cities-dropdown>

            <airlines-dropdown
                :airlines="allAirlines"
            ></airlines-dropdown>

            <date-input
                :title="'Departure'"
                :startDate="null"
                :endDate="arrival"
                :updateDate="updateStartDate"
            ></date-input>

            <date-input
                :title="'Arrival'"
                :startDate="departure"
                :endDate="null"
                :updateDate="updateEndDate"
            ></date-input>

    </div>

</template>
