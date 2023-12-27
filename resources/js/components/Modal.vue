<script>
    import DateInput from './DateInput.vue';
    import ObjectsDropdown from './ObjectsDropdown.vue';

    export default {
        components: { ObjectsDropdown, DateInput },

        data() {
            return {
                dialog: null,
                airlineId: null,
                originId: null,
                destinationId: null,
                departureDateTime: null,
                arrivalDateTime: null,
            }
        },

        props: {
            show: Boolean,
            airlines: Array,
            cities: Array,
            params: Object,
            errors: Object
        },

        mounted() {
            this.dialog = this.$refs.dialog
        },

        computed: {
            allowedOrigins() {
                if (this.airlineId) {
                    const airline = this.airlines.find(item => item.id === this.airlineId)
                    return airline.cities
                        .filter(city => city.id !== this.destinationId)
                        .sort((a, b) => a.name.localeCompare(b.name))
                }

                return this.cities.filter(city => city.id !== this.destinationId)
            },

            allowedDestinations() {
                if (this.airlineId) {
                    const airline = this.airlines.find(item => item.id === this.airlineId)
                    return airline.cities
                        .filter(city => city.id !== this.originId)
                        .sort((a, b) => a.name.localeCompare(b.name))
                }

                return this.cities.filter(city => city.id !== this.originId)
            }
        },

        watch: {
            params(newValue) {
                this.airlineId = newValue.airlineId

                this.originId =  newValue.originId
                this.destinationId = newValue.destinationId

                this.departureDateTime = newValue.departure
                this.arrivalDateTime = newValue.arrival
            },

            show(newValue) {
                if (newValue) {
                    this.dialog.showModal()
                } else {
                    this.closeModal()
                    this.$emit('update:show', newValue)
                }
            }
        },

        methods: {
            closeModal() {
                const resetParams = {
                    title: '',
                    edit: false,
                    flightId: null,
                    originId: null,
                    destinationId: null,
                    airlineId: null,
                    departure: null,
                    arrival: null,
                }

                const modalErrors = {
                    airline_id: null,
                    origin_city_id: null,
                    dest_city_id: null,
                    departure_at: null,
                    arrival_at: null
                }

                this.$emit("update:params", resetParams)
                this.$emit("update:errors", modalErrors)
                this.dialog.close()
            },

            handleModalSubmit() {
                const requestParams = {
                    origin_city_id: this.originId,
                    dest_city_id: this.destinationId,
                    airline_id: this.airlineId,
                    departure_at: (this.departureDateTime) ? this.departureDateTime.replace("T", " ") : null,
                    arrival_at: (this.arrivalDateTime) ? this.arrivalDateTime.replace("T", " ") : null
                }

                if (this.params.edit) {
                    this.$emit('editFlight', requestParams, this.params.flightId)
                } else {
                    this.$emit('createFlight', requestParams)
                }
            },

            resetCities(newAirlineId) {
                this.airlineId = newAirlineId
                this.originId = 0
                this.destinationId = 0
            }
        }
    }
</script>

<template>
    <dialog class="w-2/5 rounded-lg" ref="dialog">
    <div>
        <!-- Modal Header -->
        <div class="text-white px-4 py-2 flex justify-between rounded-lg bg-indigo-500">
            <h2 id="modal-title" class="text-lg font-semibold">{{ params.title }}</h2>
        </div>

        <!-- Modal Body -->
        <div id="modal-content" class="p-4 border-b">
            <!-- Select Airline -->
            <div class="flex flex-col">
                <objects-dropdown
                    title="Airline"
                    :objects="airlines"
                    v-model:selectedId="airlineId"
                    @update:selectedId="resetCities"
                ></objects-dropdown>
                <p
                    class="text-xs text-red-500 pl-4 mb-4"
                    v-if="errors.airline_id"
                >{{ errors.airline_id }}</p>
            </div>

            <!-- Select Origin & Destination -->
            <div class="flex flex-row">
                <div class="flex flex-col">
                    <objects-dropdown
                        title="Origin"
                        :objects="allowedOrigins"
                        v-model:selectedId="originId"
                    ></objects-dropdown>
                    <p
                        class="text-xs text-red-500 pl-4 mb-4"
                        v-if="errors.origin_city_id"
                    >{{ errors.origin_city_id }}</p>
                </div>

                <div class="flex flex-col">
                    <objects-dropdown
                        title="Destination"
                        :objects="allowedDestinations"
                        v-model:selectedId="destinationId"
                    ></objects-dropdown>
                    <p
                        class="text-xs text-red-500 pl-4 mb-4"
                        v-if="errors.dest_city_id"
                    >{{ errors.dest_city_id }}</p>
                </div>
            </div>

            <!-- Select departure dateTime, & arrival dateTime -->
            <div class="flex flex-row">
                <div class="flex flex-col">
                    <date-input
                        title="Departure"
                        inputType="datetime-local"
                        :endDate="arrivalDateTime"
                        v-model:date="departureDateTime"
                    ></date-input>
                    <p
                        class="text-xs text-red-500 pl-4 mb-2"
                        v-if="errors.departure_at"
                    >{{ errors.departure_at }}</p>
                </div>

                <div class="flex flex-col">
                    <date-input
                        title="Arrival"
                        inputType="datetime-local"
                        :startDate="departureDateTime"
                        v-model:date="arrivalDateTime"
                    ></date-input>
                    <p
                        class="text-xs text-red-500 pl-4 mb-2"
                        v-if="errors.arrival_at"
                    >{{ errors.arrival_at }}</p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div id="modal-footer" class="px-4 py-2 flex justify-end">
            <button
                id="modal-submit-btn"
                class="bg-indigo-600 hover:bg-indigo-400 text-white font-semibold py-2 px-4 text-white rounded-xl w-1/4 mx-1"
                @click="handleModalSubmit"
            >Submit</button>
            <button
                id="modal-close-btn"
                class="bg-gray-600 hover:bg-gray-400 text-white font-semibold py-2 px-4 text-white rounded-xl w-1/4 mx-1"
                @click="show = false"
            >Close</button>
        </div>
    </div>
</dialog>
</template>
