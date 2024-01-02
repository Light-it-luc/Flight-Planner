<script>
    import FlightFliters from './FlightFliters.vue';
    import Modal from './Modal.vue';
    import VueTable from './Table.vue';
    import PaginationLinks from './PaginationLinks.vue';
    import { toast } from 'vue3-toastify';
    import "vue3-toastify/dist/index.css";
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

            axios.get("api/v1/cities/all")
            .then(res => this.cities = res.data)
            .catch(err => this.errorToast("Error loading cities for filter component"))

            axios.get("api/v1/airlines/all")
            .then(res => this.airlines = res.data)
            .catch(err => this.errorToast("Error loading airlines for filter component"))
        },

        methods: {
            reloadFlights() {
                axios.get(`api/v1/flights?${this.queryParams.toString()}`)
                .then(res => {
                    this.flights = res.data.data
                    this.links = res.data.links
                })
                .catch(err => this.errorToast("Oops! An error occurred when loading flights"))
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
                const departure = new Date(flight.departure_at)
                const arrival = new Date(flight.arrival_at)

                this.modalParams = {
                    title: `Edit Flight ${flight.flight_number}`,
                    edit: true,
                    flightId: flight.id,
                    originId: flight.origin.id,
                    destinationId: flight.destination.id,
                    airlineId: flight.airline_id,
                    departure: this.parseDateTime(departure),
                    arrival: this.parseDateTime(arrival)
                }

                this.modalShow = true
            },

            parseDateTime(dateTime) {
                const date = dateTime.toISOString().substring(0, 10)
                const time = dateTime.toTimeString().substring(0, 5)
                return `${date} ${time}`
            },

            successToast(msg) {
                toast(msg, {
                    autoClose: 2000,
                    theme: "auto",
                    type: "success"
                })
            },

            errorToast(msg) {
                toast(msg, {
                    autoClose: 3000,
                    theme: "auto",
                    type: "error"
                })
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
        @toast-success="successToast"
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
        @toast-success="successToast"
        @toast-error="errorToast"
    ></vue-table>

    <pagination-links
        :links="links"
        v-model:queryParams="queryParams"
    ></pagination-links>
</template>
