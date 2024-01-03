<script>
    export default {

        props: {
            flights: Array,
            tableName: String,
            columnTitles: Array
        },

        methods: {
            handleDeleteFlight(flight) {
                const confirm = window.confirm(
                    `Delete Flight Number ${flight.flight_number}` +
                    `(${flight.origin.name} - ${flight.destination.name})?`
                )

                if (confirm) {
                    axios.delete(`api/v1/flights/${flight.id}`)
                        .then(res => {
                            this.$emit("reloadFlights")
                            this.$emit("toastSuccess", "Flight deleted.")
                        })
                        .catch(err => {
                            this.$emit("toastError", err.message)
                        })
                }
            }
        }
    }
</script>

<template>
    <table class="w-full text-sm text-center">
        <caption class="hidden">{{ tableName }}</caption>
        <thead class="text-xs text-gray-800 uppercase border-b border-gray-300">
            <tr>
                <th
                    v-for="column in columnTitles"
                    :key="column"
                    scope="col" class="py-3 px-6"
                >{{ column }}</th>

                <th scope="col" class="py-3 px-6"></th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="flight in flights"
                :key="flight.id"
                class="bg-white border-b border-gray-100"
            >
                <td class="py-4 px-6">{{ flight.flight_number }}</td>
                <td class="py-4 px-6">{{ flight.origin.name }}</td>
                <td class="py-4 px-6">{{ flight.destination.name }}</td>
                <td class="py-4 px-6">{{ flight.departure_at.slice(0, -3) }}</td>
                <td class="py-4 px-6">{{ flight.arrival_at.slice(0, -3) }}</td>
                <td class="py-4 px-6">
                    <div class="space-x-2">
                        <button class="edit-btn dark:bg-indigo-600 hover:bg-indigo-400 text-white
                        font-semibold py-2 px-4 text-white rounded-xl"
                        @click="$emit('editFlightModal', flight)"
                        >Edit</button>

                        <button class="edit-btn dark:bg-red-600 hover:bg-red-400 text-white
                        font-semibold py-2 px-4 text-white rounded-xl"
                        @click="handleDeleteFlight(flight)"
                        >Delete</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>
