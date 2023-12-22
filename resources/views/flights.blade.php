<x-layout :title="'Flights'">

    <div id="app" class="max-w-7xl m-auto mt-8">

        <global-flights></global-flights>

        <div class="overflow-x-auto relative">

        <x-table
        :tableName="'Flights'"
        :columnTitles="['Flight Number', 'Origin', 'Destination', 'Departure', 'Arrival']">
        </x-table>

        </div>
    </div>

    <div id="pages-container" class="mt-12 mb-4 px-12"></div>

    <script>
        /* const displayModal = (title, body, footer='', color='bg-indigo-500') => {
            const modal = $(`#modal`)[0]
            const titleContainer = $('#modal-title').closest('div')

            $(titleContainer).removeClass('bg-indigo-500 bg-red-500').addClass(color);

            $(`#modal-title`).text(title)
            $(`#modal-content`).html(body)
            $(`#modal-close-btn`).before(footer)

            modal.showModal()
        }

        const closeModal = () => {
            const modal = $(`#modal`)[0]
            const closeBtn = $(`#modal-close-btn`)

            $(`#modal-title`).text('')
            $(`#modal-content`).html('')
            $(`#modal-footer`).html(closeBtn)
            modal.close()
        }

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

        const getInputValues = (...inputNames) => {
            let values = []

            for (const name of inputNames) {
                const node = $(`input[name=${name}]`)
                if (node) {
                    values.push($(node).val())
                }
            }

            return values
        }

        // New functions here
        const getSelectedId = (selectBoxId) => {
            const selectedLi = $(`${selectBoxId} #undefined-dropdown ul .is-selected`)
            const multiselectId = $(selectedLi).attr("id")

            if (multiselectId) {
                return multiselectId.split('-')[2]
            }

            return '0'
        }

        const getFilters = () => {
            return {
                originId: Number(getSelectedId("#select-origin")),
                destId: Number(getSelectedId("#select-destination")),
                airlineId: Number(getSelectedId("#select-airlines")),
                departure: $('#select-departure').val(),
                arrival: $("#select-arrival").val()
            }
        }

        const getCreateInputs = () => {
            return {
                origin_city_id: Number(getSelectedId("#vue-modal #select-origin")),
                dest_city_id: Number(getSelectedId("#vue-modal #select-destination")),
                airline_id: Number(getSelectedId("#vue-modal #select-airline")),
                departure_at: $('#vue-modal #select-departure-dt').val().replace("T", " "),
                arrival_at: $("#vue-modal #select-arrival-dt").val().replace("T", " ")
            }
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
            populateFlightsTable()

            $("#modal").on("click", "#modal-close-btn", closeModal)

            $("#create-button").click(() => {
                $("#vue-modal")[0].showModal()
            })

            $("#vue-modal").on("click", "#modal-close-btn", () => {
                $("#vue-modal")[0].close()
            })

            $("#vue-modal").on("click", "#modal-submit-btn", () => {
                const values = getCreateInputs()
                const headers = {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }

                axios.post("api/v1/flights", values, headers)
                    .then(res => {
                        alert(`Success! Flight ${res.data.flight_number} was created.`)
                        clearFlightsTable()
                        populateFlightsTable()
                    })
                    .catch(err => console.log(err))
            })

            $("#filter-button").click(() => {
                const filters = getFilters()
                updateQueryParams(filters)
                populateFlightsTable()
            })

            $("tbody").on("click", ".del-btn", (event) => {
                const row = $(event.target).closest("tr")
                const flightId = $(row).attr("flight-id")
                const flightNumber = $(row)
                    .children("td")
                    .first()
                    .text()


                const content = `
                <input type="hidden" name="_id" value="${flightId}" />
                <p>Are you sure you want to delete flight ${flightNumber}</p>
                `

                const confirmBtn = `
                <button id="modal-delete-btn"
                    class="bg-red-500 hover:bg-red-300 text-white
                    font-semibold py-2 px-4 text-white rounded-xl mx-2"
                >Confirm</button>
              `

                return displayModal("Warning", content, confirmBtn, "bg-red-500")
            })

            $("#modal").on("click", "#modal-delete-btn", () => {
                const [id] = getInputValues("_id")

                axios.delete(`api/v1/flights/${id}`)
                .then(res => {
                    // Flash toast
                    populateFlightsTable()
                    closeModal()
                })
                .catch(err => console.log(err))
            })

            $("#pages-container").on("click", ".page-btn", (event) => {
                const queryParams = $(event.target)
                .attr("url")
                .split("?")[1]

                history.pushState(null, "", `flights?${queryParams}`)
                populateFlightsTable()
          })
        }) */
    </script>
</x-layout>

