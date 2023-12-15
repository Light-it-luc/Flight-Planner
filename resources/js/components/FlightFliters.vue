<script>
    import CitiesDropdown from './CitiesDropdown.vue';
    import AirlinesDropdown from './AirlinesDropdown.vue';
    import axios from 'axios';

    export default {

        components: { CitiesDropdown, AirlinesDropdown },

        data() {
            return {
                cities: [],
                airlines: [],
                originCityId: 0,
                destinationCityId: 0,
                airlineId: 0
            }
        },

        created() {
            axios.get("api/v1/cities")
            .then(res => this.cities = res.data.data)
            .catch(err => console.log(err))

            axios.get("api/v1/airlines")
            .then(res => this.airlines = res.data.data)
            .catch(err => console.log(err))
        },

        computed: {
            allCities() {
                const all = { id: 0, name: "All Cities", country: "" }
                return [all, ...this.cities]
            },

            allAirlines() {
                const all = { id: 0, name:"All Airlines", description:"" }
                return [all, ...this.airlines]
            }
        },

        methods: {
            handleCityChange(eventData) {
                if (eventData.origin) {
                    this.originCityId = eventData.selectedCityId
                } else {
                    this.destinationCityId = eventData.selectedCityId
                }
            }
        }
    }
</script>

<template>

    <div class="flex flex-row mx-24">
            <cities-dropdown
                :cities="allCities"
                :disabledCity="destinationCityId"
                :origin="true"
                :title="'Origin'"
                @changeCity="handleCityChange"
            ></cities-dropdown>

            <cities-dropdown
                :cities="allCities"
                :disabledCity="originCityId"
                :origin="false"
                :title="'Destination'"
                @changeCity="handleCityChange"
            ></cities-dropdown>

            <airlines-dropdown
                :airlines="allAirlines"
            ></airlines-dropdown>
    </div>

</template>
