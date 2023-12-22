<script>
    import DateTimeInput from './DateTimeInput.vue';
    import ObjectsDropdown from './ObjectsDropdown.vue';

    export default {
        components: { ObjectsDropdown, DateTimeInput },

        data() {
            return {
                dialog: null,
                airlineId: null,
                originId: null,
                destinationId: null,
                departureDateTime: null,
                arrivalDateTime: null
            }
        },

        props: {
            show: Boolean,
            title: String,
            airlines: Array,
            cities: Array,
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
            airlineId(oldValue, newValue) {
                this.originId = 0
                this.destinationId = 0
            },

            show(newValue) {
                if (newValue) {
                    this.dialog.showModal()
                } else {
                    this.closeModal()
                }

                this.$emit('update:show', newValue)
            }
        },

        methods: {
            closeModal() {
                this.airlineId = 0
                this.dialog.close()
            }
        }
    }
</script>

<template>
    <dialog id="vue-modal" class="w-2/5 border rounded-lg" ref="dialog">
    <div>
        <!-- Modal Header -->
        <div class="text-white px-4 py-2 flex justify-between rounded-lg bg-indigo-500">
            <h2 id="modal-title" class="text-lg font-semibold">{{ title }}</h2>
        </div>

        <!-- Modal Body -->
        <div id="modal-content" class="p-4 border-b">
            <!-- Select Airline -->
            <div>
                <objects-dropdown
                    title="Airline"
                    :objects="airlines"
                    v-model:selectedId="airlineId"
                ></objects-dropdown>
            </div>

            <!-- Select Origin & Destination -->
            <div class="flex flex-row">
                <objects-dropdown
                    title="Origin"
                    :objects="allowedOrigins"
                    v-model:selectedId="originId"
                ></objects-dropdown>

                <objects-dropdown
                    title="Destination"
                    :objects="allowedDestinations"
                    v-model:selectedId="destinationId"
                ></objects-dropdown>
            </div>

            <!-- Select departure dateTime, & arrival dateTime -->
            <div class="flex flex-row">
                <date-time-input
                    title="Departure"
                    selectBoxId="select-departure-dt"
                    :startDateTime="null"
                    :endDateTime="arrivalDateTime"
                    v-model:date="departureDateTime"
                ></date-time-input>

                <date-time-input
                    title="Arrival"
                    selectBoxId="select-arrival-dt"
                    :startDateTime="departureDateTime"
                    :endDateTime="null"
                    v-model:date="arrivalDateTime"
                ></date-time-input>
            </div>
        </div>

        <!-- Modal Footer -->
        <div id="modal-footer" class="px-4 py-2 flex justify-end">
            <button
                id="modal-submit-btn"
                class="bg-indigo-600 hover:bg-indigo-400 text-white font-semibold py-2 px-4 text-white rounded-xl w-1/4 mx-1"
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
