<x-layout :title="'Flights'">

    <x-modal/>

    <div id="app" class="max-w-7xl m-auto mt-8">

        <flight-filters></flight-filters>

        <div class="overflow-x-auto relative">

        <x-table
        :tableName="'Flights'"
        :columnTitles="['Flight Number', 'Origin', 'Destination', 'Departure', 'Arrival']">
        </x-table>

        </div>
    </div>

    <div id="pages-container" class="mt-12 mb-4 px-12"></div>

    <script>
        const regeneratePaginationLinks = (links) => {
            clearPaginationLinks()
            const btnContainer = $("#pages-container")

            links.forEach(link => {
                let btnText = link.label.replace("&laquo;", "").replace("&raquo;", "")
                const btn = $("<button>")
                    .addClass("text-black p-1 border border-gray-500 min-w-fit px-4 rounded-lg m-2")
                    .addClass(link.active ? "bg-gray-300" : "bg-white");

                if (link.url) {
                    btn.addClass("page-btn").attr("url", link.url)
                }

                btn.text(btnText);
                btnContainer.append(btn)
            });
        }

        const clearPaginationLinks = () => {
            $('#pages-container button').remove()
        }

        // New functions here
        const getSelectedValue = (selectId) => {
            return $(`#${selectId}`).find(":selected").val()
        }

        const updateQueryParams = (filters) => {
            let queryParams = new URLSearchParams(window.location.search);

            const mappings = {
                originId: 'origin',
                destId: 'destination',
                airlineId: 'airline',
                departure: 'departure',
                arrival: 'arrival'
            }

            for (const [key, value] of Object.entries(filters)) {
                if (value) {
                    const queryParamKey = mappings[key]
                    queryParams.set(queryParamKey, value)
                } else {
                    const queryParamKey = mappings[key]
                    queryParams.delete(queryParamKey)
                }
            }

            history.pushState(null, "", `flights?${queryParams.toString()}`)
        }

        const getAirlineFilters = () => {
            const filters = {
                originId:Number(getSelectedValue("select-origin")),
                destId: Number(getSelectedValue("select-destination")),
                airlineId: Number(getSelectedValue("select-airline")),
                departure: $("#select-departure").val(),
                arrival: $("#select-arrival").val()
            }

            return filters
        }

        const clearFlightsTable = () => {
            $('tr[flight-id]').remove()
        }

        const populateFlightsTable = () => {
            let queryParams = new URLSearchParams(window.location.search);

            axios.get(`api/v1/flights?${queryParams.toString()}`)
            .then(response => {
                clearFlightsTable()
                clearPaginationLinks()

                const flights = response.data.data
                flights.forEach(flight => addRowInFlightsTable(flight))

                regeneratePaginationLinks(response.data.links)
            })
        }

        const addRowInFlightsTable = (flight) => {
            const btnClass = 'text-white font-semibold py-2 px-4 text-white rounded-xl'

            $('tbody').append(
                `<tr flight-id=${flight.id} class="bg-white border-b border-gray-100">
                    <td class="py-4 px-6">${flight.flight_number}</td>
                    <td class="py-4 px-6">${flight.origin.name}</td>
                    <td class="py-4 px-6">${flight.destination.name}</td>
                    <td class="py-4 px-6">${flight.departure_at}</td>
                    <td class="py-4 px-6">${flight.arrival_at}</td>
                    <td class="py-4 px-6">
                    <div id="btn-container" class="flex flex-row">
                        <button id="${flight.id}"
                                class="${btnClass} edit-btn dark:bg-indigo-600 hover:bg-indigo-400"
                            >Edit</button>
                        <button id="${flight.id}"
                                class="${btnClass} del-btn ml-2 dark:bg-red-600 hover:bg-red-400"
                            >Delete</button>
                    </div>
                    </td>
                </tr>`
                )
        }

        $(document).ready(() => {
            $(".select2").select2()

            populateFlightsTable()

            $("#filter-button").click(() => {
                const filters = getAirlineFilters()
                updateQueryParams(filters)
                populateFlightsTable()
            })

            $("#pages-container").on("click", ".page-btn", (event) => {
                const queryParams = $(event.target)
                .attr("url")
                .split("?")[1]

                history.pushState(null, "", `flights?${queryParams}`)
                populateFlightsTable()
          })
        })
    </script>
</x-layout>

