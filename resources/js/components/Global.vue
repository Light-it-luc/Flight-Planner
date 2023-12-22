<script>
    import FlightFliters from './FlightFliters.vue';
    import Modal from './Modal.vue';
    import axios from 'axios';

    export default {

        components: { FlightFliters, Modal },

        data() {
            return {
                cities: [],
                airlines: [],
                flights: [],
                links: [],
                modalShow: false,
                modalTitle: '',
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
            showCreateModal() {
                this.modalTitle = 'Create New Flight'
                this.modalShow = true
            }
        },

        watch: {
            queryParams(newValue) {
                history.pushState(null, "", "?" + newValue)

                axios.get(`api/v1/flights?${newValue}`)
                .then(res => {
                    this.flights = res.data.data
                    this.links = res.data.links
                })
                .catch(err => console.log(err))
            }
        }
    }
</script>

<template>
    <modal
        :airlines="airlines"
        :cities="cities"
        v-model:show="modalShow"
        v-model:title="modalTitle"
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
</template>
