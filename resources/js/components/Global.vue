<script>
    import FlightFliters from './FlightFliters.vue';
    import Modal from './Modal.vue';
    import VueTable from './Table.vue';
    import Links from './Links.vue';
    import axios from 'axios';

    export default {

        components: { FlightFliters, Modal, VueTable, Links },

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
                modalErrors: {
                    airline_id: null,
                    origin_city_id: null,
                    dest_city_id: null,
                    departure_at: null,
                    arrival_at: null
                },
                queryParams: null
            }
        },

        created() {
            this.queryParams = (new URLSearchParams(window.location.search)).toString()

            axios.get("api/v1/cities?all=true")
            .then(res => this.cities = res.data)
            .catch(err => console.log(err))

            axios.get("api/v1/airlines?all=true")
            .then(res => this.airlines = res.data)
            .catch(err => console.log(err))
        },

        methods: {
            reloadFlights() {
                axios.get(`api/v1/flights?${this.queryParams}`)
                .then(res => {
                    this.flights = res.data.data
                    this.links = res.data.links
                })
                .catch(err => console.log(err))
            },

            clearModalErrors() {
                this.modalErrors = {
                    airline_id: null,
                    origin_city_id: null,
                    dest_city_id: null,
                    departure_at: null,
                    arrival_at: null
                }
            },

            populateModalErrors(err) {
                this.modalErrors = {
                    airline_id: (err.airline_id)? err.airline_id.join(". "): null,
                    origin_city_id: (err.origin_city_id)? err.origin_city_id.join(". "): null,
                    dest_city_id: (err.dest_city_id)? err.dest_city_id.join(". "): null,
                    departure_at: (err.departure_at)? err.departure_at.join(". "): null,
                    arrival_at: (err.arrival_at)? err.arrival_at.join(". "): null
                }
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
                    departure: flight.departure_at.replace(" ", "T").slice(0, -3),
                    arrival: flight.arrival_at.replace(" ", "T").slice(0, -3)
                }

                this.modalShow = true
            },

            handleCreateFlight(requestParams) {
                axios.post("api/v1/flights", requestParams)
                    .then(res => {
                        this.clearModalErrors()
                        this.reloadFlights()
                        this.modalShow = false
                        alert("Creation successfull")
                    })
                    .catch(err => this.populateModalErrors(err.response.data.errors))
            },

            handleEditFlight(requestParams, id) {
                axios.patch(`api/v1/flights/${id}`, requestParams)
                    .then(res => {
                        this.clearModalErrors()
                        this.reloadFlights()
                        this.modalShow = false
                        alert("Edit successfull")
                    })
                    .catch(err => this.populateModalErrors(err.response.data.errors))
            },

            handleDeleteFlight(flight) {
                const confirm = window.confirm(
                    `Delete Flight Number ${flight.flight_number}` +
                    `(${flight.origin.name} - ${flight.destination.name})?`
                )

                if (confirm) {
                    axios.delete(`api/v1/flights/${flight.id}`)
                        .then(res => {
                            this.reloadFlights()
                            alert("Flight deleated")
                        })
                        .catch(err => console.log(err))
                }
            }
        },

        watch: {
            queryParams(newValue) {
                history.pushState(null, "", "?" + newValue)
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
        v-model:errors="modalErrors"
        @edit-flight="handleEditFlight"
        @create-flight="handleCreateFlight"
    ></modal>

    <div class="my-8">
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
        :flights="flights"
        tableName="Flights"
        :columnTitles="['Flight Number', 'Origin', 'Destination', 'Departure', 'Arrival']"
        @edit-flight-modal="showEditModal"
        @delete-flight="handleDeleteFlight"
    ></vue-table>

    <links
        :links="links"
        v-model:queryParams="queryParams"
    ></links>
</template>
