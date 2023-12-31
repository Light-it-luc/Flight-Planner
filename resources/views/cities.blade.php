<x-layout :title="'Cities'">

    <x-modal/>

    <div class="max-w-6xl m-auto mt-8">
        <x-airlines-dropdown/>
        <div class="overflow-x-auto relative">

        <x-table
        :tableName="'Cities'" :columnTitles="['ID', 'Name', 'Country', 'Incoming Flights', 'Outgoing Flights']">
        <tr id="create-row" class="bg-white border-b border-gray-100">
            <td class="py-4 px-6"></td>
            <td class="py-4 px-6">
                <x-input :name="'name'"></x-input>
            </td>
            <td class="py-4 px-6">
                <x-input :name="'country'"></x-input>
            </td>
            <td class="py-4 px-6"></td>
            <td class="py-4 px-6"></td>
            <td class="py-4 px-6">
                <x-button id="create-button" class="dark:bg-gray-500 hover:bg-gray-400">Create</x-button>
            </td>
        </tr>

        </x-table>

        </div>
    </div>

    <div id="pages-container" class="mt-12 mb-4 px-12"></div>

    <script type="module">
        const populateCitiesTable = () => {
            let queryParams = new URLSearchParams(window.location.search);

            $.ajax(`api/v1/cities?${queryParams.toString()}`, {
                success: function(response) {
                    const cities = response.data
                    clearCitiesTable()
                    cities.forEach(city => addRowInCityTable(city))
                    regeneratePaginationLinks(response.links)
                },
                error: function(err) {
                    const content = parseErrorMessages(err.responseJSON.errors)
                    return displayModal("Error", content, "", "bg-red-500")
                }
            })
        }

        const clearCitiesTable = () => {
            $('tr[city-id]').remove()
        }

        const addRowInCityTable = (city) => {
            const btnClass = 'text-white font-semibold py-2 px-4 text-white rounded-xl'
            const incomingFlights = city.flights_to_count ? city.flights_to_count : '0'
            const outgoingFlights = city.flights_from_count? city.flights_from_count: '0'

            $('tbody').append(
                `<tr city-id=${city.id} class="bg-white border-b border-gray-100">
                    <td class="py-4 px-6">${city.id}</td>
                    <td class="py-4 px-6">${city.name}</td>
                    <td class="py-4 px-6">${city.country}</td>
                    <td class="py-4 px-6">${incomingFlights}</td>
                    <td class="py-4 px-6">${outgoingFlights}</td>
                    <td class="py-4 px-6">
                    <div id="btn-container" class="flex flex-row">
                        <button id="${city.id}"
                                class="${btnClass} edit-btn dark:bg-indigo-600 hover:bg-indigo-400"
                            >Edit</button>
                        <button id="${city.id}"
                                class="${btnClass} del-btn ml-2 dark:bg-red-600 hover:bg-red-400"
                            >Delete</button>
                    </div>
                    </td>
                </tr>`
                )
        }

        const getCellsInRow = (row) => {
            const cells = $(row).children('td')

            const id = $(cells[0])
            const name = $(cells[1])
            const country = $(cells[2])

            return [id, name, country]
        }

        const handleCityEditErrors = (errors) => {
            const nameInput = $('input[name="edit-name"]')
            const countryInput = $('input[name="edit-country"]')

            $('.modal-edit-error').remove()

            if (Object.hasOwn(errors, 'name')) {
            let nameErrors =
                `<p class="modal-edit-error text-xs text-red-500 mb-3">${errors.name.join(" ")}</p>`
                $(nameInput).after(nameErrors)
            }
            if (Object.hasOwn(errors, 'country')) {
                let countryErrors =
                `<p class="modal-edit-error text-xs text-red-500 mb-3">${errors.country.join(" ")}</p>`
                $(countryInput).after(countryErrors)
            }
        }

        const colorCityTableHeadersOnSort = () => {
            const queryParams = new URLSearchParams(window.location.search);
            const sortOrder = queryParams.get("sort_by")

            if (sortOrder === "name") {
                $("#col-name").addClass("bg-gray-100")
                $("#col-country").removeClass("bg-gray-100")
                $("#col-id").removeClass("bg-gray-100")
            } else if(sortOrder === "country") {
                $("#col-country").addClass("bg-gray-100")
                $("#col-name").removeClass("bg-gray-100")
                $("#col-id").removeClass("bg-gray-100")
            } else {
                $("#col-id").addClass("bg-gray-100")
                $("#col-name").removeClass("bg-gray-100")
                $("#col-country").removeClass("bg-gray-100")
            }
        }

        const sortCityTable = (columnName) => sortTableBy(columnName, "cities", () => {
            colorCityTableHeadersOnSort()
            populateCitiesTable()
        })

        $(document).ready(() => {

          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          })

          colorCityTableHeadersOnSort()
          populateCitiesTable()

          $('#modal').on('click', '#modal-close-btn', closeModal)

          $('table').on('click', '#create-button', () => {
              const city = {
                  name: $('input[name="name"]').val().trim(),
                  country: $('input[name="country"]').val().trim()
              }

              $.ajax({
                  type: 'POST',
                  url: 'api/v1/cities',
                  data: city,
                  dataType: 'json',
                  success: function(data) {
                      $('input[name="name"]').val('')
                      $('input[name="country"]').val('')

                      populateCitiesTable()
                  },
                  error: function (err) {
                    const content = parseErrorMessages(err.responseJSON.errors)
                    displayModal('Creation failed', content, '', 'bg-red-500')
                  }
              })
          })

          $('table').on('click', 'button.edit-btn', (event) => {
              const [id, name, country] = getCellsInRow($(event.target).closest('tr'))
                .map(cell => $(cell).text())

              const content = `
                <div class="flex flex-column">
                    <div>
                        <div class="my-2">
                            <input type="hidden" name="_id" value="${id}">
                            <label class="block mb-1 uppercase font-bold text-sm text-gray-700"
                                   for="edit-name">
                                   Name
                            </label>
                            <input type="text" name="edit-name" value="${name}" required
                                class="mb-2 p-1 text-center border border-gray-300 text-black
                                placeholder-gray-300 rounded-lg">
                        </div>
                        <div class="my-2">
                            <label class="block mb-1 uppercase font-bold text-sm text-gray-700"
                                   for="edit-country">Country</label>
                            <input type="text" name="edit-country" value="${country}" equired
                                class="mb-2 p-1 text-center border border-gray-300 text-black
                                placeholder-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>`

              const submitBtn = `
                <button id="modal-edit-btn"
                    class="bg-indigo-500 hover:bg-indigo-300 text-white font-semibold py-2 px-4
                    text-white rounded-xl mx-2">Update</button>
              `

              displayModal('Edit City', content, submitBtn)
          })

          $('#modal').on('click', '#modal-edit-btn', () => {
            const [id, name, country] = getInputValues('_id', 'edit-name', 'edit-country')

            const updateCity = {
                name: name.trim(),
                country: country.trim()
            }

            $.ajax({
                type: 'PUT',
                url: 'api/v1/cities/' + id,
                data: updateCity,
                dataType: 'json',
                success: function(data) {
                    populateCitiesTable()
                    closeModal()
                },
                error: function (err) {
                    handleCityEditErrors(err.responseJSON.errors)
                }
            })
          })

          $('table').on('click', 'button.del-btn', (event) => {
            const [id, name, country] = getCellsInRow($(event.target).closest('tr'))
                .map(cell => $(cell).text())

            const content = `
                <p>Are you sure you want to delete ${name} (${country}) from the database?</p>
                <input type="hidden" name="_id" value="${id}">
            `
            const confirmBtn = `
                <button id="modal-delete-btn"
                    class="bg-red-500 hover:bg-red-300 text-white
                    font-semibold py-2 px-4 text-white rounded-xl mx-2"
                >Confirm</button>
              `
            displayModal('Warning', content, confirmBtn, 'bg-red-500')
          })

          $('#modal').on('click', '#modal-delete-btn', () => {
            const [id] = getInputValues("_id")

            $.ajax({
                type: 'DELETE',
                url: 'api/v1/cities/' + id,
                dataType: 'json',
                success: function() {
                    populateCitiesTable()
                    closeModal()
                },
                error: function (err) {
                    const content = parseErrorMessages(err.responseJSON.errors)
                    displayModal('Edit Failed', content, '', 'bg-red-500')
                }
            })
          })

          $('#pages-container').on("click", ".page-btn", (event) => {
            const queryParams = $(event.target)
              .attr("url")
              .split("?")[1]

            history.pushState(null, "", "cities?" + queryParams.toString())
            populateCitiesTable()

          })

          $("#col-id").click(() => sortCityTable("id"))

          $("#col-name").click(() => sortCityTable("name"))

          $("#col-country").click(() => sortCityTable("country"))

          $("#airline-filter").change((event) => {
            let queryParams = new URLSearchParams(window.location.search);
            const selected = $(event.target).find("option:selected").attr("airline-id")

            if (selected) {
                queryParams.set("airline", selected)
            } else {
                queryParams.delete("airline")
            }

            history.pushState(null, "", `cities?${queryParams.toString()}`)
            populateCitiesTable()
          })
        })
      </script>
</x-layout>

