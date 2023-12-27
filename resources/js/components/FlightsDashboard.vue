<script>
    import FlightFliters from './FlightFliters.vue';
    import Modal from './Modal.vue';
    import VueTable from './Table.vue';
    import PaginationLinks from './PaginationLinks.vue';
    import axios from 'axios';

    export default {

        components: { FlightFliters, Modal, VueTable, PaginationLinks },

        data() {
            return {
                cities: [],
                airlines: [],
                flights: [],
                links: [],
                modalShow: false,
                modalParams: {
                    title: '',
                    edit: false,
                    flightId: null,
                    originId: null,
                    destinationId: null,
                    airlineId: null,
                    departure: null,
                    arrival: null
                },
                queryParams: null,
            }
        },

        created() {
            this.queryParams = new URLSearchParams(window.location.search)

            axios.get("api/v1/cities?all=true")
            .then(res => this.cities = res.data)
            .catch(err => console.log(err))

            axios.get("api/v1/airlines?all=true")
            .then(res => this.airlines = res.data)
            .catch(err => console.log(err))
        },

        methods: {
            reloadFlights() {
                axios.get(`api/v1/flights?${this.queryParams.toString()}`)
                .then(res => {
                    this.flights = res.data.data
                    this.links = res.data.links
                })
                .catch(err => console.log(err))
            },

            showCreateModal() {
                this.modalParams = {
                    title: 'Create New Flight',
                    edit: false,
                    flightId: null,
                    originId: null,
                    destinationId: null,
                    airlineId: null,
                    departure: null,
                    arrival: null
                }
                this.modalShow = true
            },

            showEditModal(flight) {
                this.modalParams = {
                    title: `Edit Flight ${flight.flight_number}`,
                    edit: true,
                    flightId: flight.id,
                    originId: flight.origin.id,
                    destinationId: flight.destination.id,
                    airlineId: flight.airline_id,
                    departure: flight.departure_at.replace("T", " ").slice(0, -3),
                    arrival: flight.arrival_at.replace("T", " ").slice(0, -3)
                }

                this.modalShow = true
            }
        },

        watch: {
            queryParams(newValue) {
                history.pushState(null, "", "?" + newValue.toString())
                this.reloadFlights()
            }
        }
    }
</script>

<template>
    <modal
        :airlines="airlines"
        :cities="cities"
        v-model:show="modalShow"
        v-model:params="modalParams"
        @reload-flights="reloadFlights"
    ></modal>

    <div class="my-4">
        <button
            class="mx-24 font-semibold text-white dark:bg-indigo-500 hover:bg-indigo-400
            w-38 py-1 px-2 rounded-md"
            @click="showCreateModal"
        >Create Flight</button>
    </div>

    <flight-fliters
        :airlines="airlines"
        :cities="cities"
        v-model:queryParams="queryParams"
    ></flight-fliters>

    <vue-table
        tableName="Flights"
        :flights="flights"
        :columnTitles="['Flight Number', 'Origin', 'Destination', 'Departure', 'Arrival']"
        @edit-flight-modal="showEditModal"
        @reload-flights="reloadFlights"
    ></vue-table>

    <pagination-links
        :links="links"
        v-model:queryParams="queryParams"
    ></pagination-links>
</template>
