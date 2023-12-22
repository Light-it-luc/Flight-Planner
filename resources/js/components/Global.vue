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
                queryParams: new URLSearchParams(window.location.search),
            }
        },

        created() {
            axios.get(`api/v1/flights?${this.queryParams.toString()}`)
            .then(res => {
                this.flights = res.data.data
                this.links = res.data.links
            })
            .catch(err => console.log(err))

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
    ></flight-fliters>

    <!-- <table-component
        :headings="..."
        :flights="flights"
    ></table-component> -->

    <!-- <pagination-links
        :links="links"
        v-model:queryParams="queryParams"
    ></pagination-links> -->
</template>
