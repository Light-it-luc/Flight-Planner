<x-layout :title="'Cities'">

    <x-modal></x-modal>

    <div class="max-w-6xl m-auto mt-8">
        <div class="overflow-x-auto relative">

        <x-table
        :tableName="'Cities'" :columnTitles="['ID', 'Name', 'Country', 'Incoming Flights', 'Outgoing Flights']"
        :firstInput="'name'" :secondInput="'country'">

            @foreach($cities as $city)
                <tr city-id="{{ $city->id }}" class="bg-white border-b border-gray-100">
                    <td class="py-4 px-6">{{ $city->id }}</td>
                    <td class="py-4 px-6">{{ $city->name }}</td>
                    <td class="py-4 px-6">{{ $city->country }}</td>
                    <td class="py-4 px-6">{{ $city->flights_to_count }}</td>
                    <td class="py-4 px-6">{{ $city->flights_from_count }}</td>
                    <td class="py-4 px-6">
                        <div id="btn-container" class="flex flex-row">
                            <x-button class="edit-btn dark:bg-indigo-600 hover:bg-indigo-400">Edit</x-button>
                            <x-button class="del-btn ml-2 dark:bg-red-600 hover:bg-red-400">Delete </x-button>
                        </div>
                    </td>
                </tr>
            @endforeach

        </x-table>

        </div>
    </div>

    <div class="mt-12 mb-4 px-12">
        {{ $cities->links() }}
    </div>

    <script>
        function displayModal(title, body, footer='', color='bg-indigo-500') {
            const modal = $(`#modal`)[0]
            const titleContainer = $('#modal-title').closest('div')

            $(titleContainer).removeClass('bg-indigo-500 bg-red-500').addClass(color);

            $(`#modal-title`).text(title)
            $(`#modal-content`).html(body)
            $(`#modal-close-btn`).before(footer)

            modal.showModal()
        }

        function closeModal() {
            const modal = $(`#modal`)[0]
            const closeBtn = $(`#modal-close-btn`)

            $(`#modal-title`).text('')
            $(`#modal-content`).html('')
            $(`#modal-footer`).html(closeBtn)
            modal.close()
        }

        function getCellsInRow(row) {
            const cells = $(row).children('td')

            const id = $(cells[0])
            const name = $(cells[1])
            const country = $(cells[2])

            return [id, name, country]
        }

        function wrapInTag(tag, text) {
            return `<${tag}>${text}</${tag}>`
        }

        function parseErrorMessages(error) {
          const validationErrors = error.responseJSON.errors
          let content = ''

          messages = Object.values(validationErrors).flat()
          messages.map(err => content += wrapInTag('li', err))

          return wrapInTag('ul', content)
        }

        $(document).ready(function () {

          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          })

          $('#modal').on('click', '#modal-close-btn', () => closeModal())

          $('table').on('click', '#create-button', function() {
              const city = {
                  name: $('input[name="name"]').val().trim(),
                  country: $('input[name="country"]').val().trim()
              }

              $.ajax({
                  type: 'POST',
                  url: '/cities',
                  data: city,
                  dataType: 'json',
                  success: function(data) {
                      const btnClass = 'text-white font-semibold py-2 px-4 text-white rounded-xl'

                      $('#create-row').after(
                          `<tr city-id=${data.id} class="bg-white border-b border-gray-100">
                              <td class="py-4 px-6">${data.id}</td>
                              <td class="py-4 px-6">${data.name}</td>
                              <td class="py-4 px-6">${data.country}</td>
                              <td class="py-4 px-6">0</td>
                              <td class="py-4 px-6">0</td>
                              <td class="py-4 px-6">
                                <div id="btn-container" class="flex flex-row">
                                  <button id="${data.id}"
                                          class="${btnClass} edit-btn dark:bg-indigo-600 hover:bg-indigo-400"
                                      >Edit</button>
                                  <button id="${data.id}"
                                          class="${btnClass} del-btn ml-2 dark:bg-red-600 hover:bg-red-400"
                                      >Delete</button>
                                </div>
                              </td>
                          </tr>`
                      )

                      $('input[name="name"]').val('')
                      $('input[name="country"]').val('')
                  },
                  error: function (err) {
                    const content = parseErrorMessages(err)
                    displayModal('Creation failed', content, '', 'bg-red-500')
                  }
              })
          })

          $('table').on('click', 'button.edit-btn', function () {
              const [id, name, country] = getCellsInRow($(this).closest('tr'))
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
                            <input
                                type="text"
                                name="edit-name"
                                value="${name}"
                                required
                                class="mb-2 p-1 text-center border border-gray-300 text-black
                                placeholder-gray-300 rounded-lg">
                        </div>
                        <div class="my-2">
                            <label class="block mb-1 uppercase font-bold text-sm text-gray-700"
                                   for="edit-country">
                                Country
                            </label>
                            <input
                                type="text"
                                name="edit-country"
                                value="${country}"
                                required
                                class="mb-2 p-1 text-center border border-gray-300 text-black
                                placeholder-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
              `
              const submitBtn = `
                <button id="modal-edit-btn"
                    class="bg-indigo-500 hover:bg-indigo-300 text-white
                    font-semibold py-2 px-4 text-white rounded-xl mx-2"
                    >Update</button>
              `

              displayModal('Edit City', content, submitBtn)
          })

          $('#modal').on('click', '#modal-edit-btn', function() {
            const modal = $('#modal')

            const id = $(modal).find('input[name="_id"]').val()
            const name = $(modal).find('input[name="edit-name"]').val()
            const country = $(modal).find('input[name="edit-country"]').val()

            const updateCity = {
                name: name.trim(),
                country: country.trim()
            }

            $.ajax({
                type: 'PATCH',
                url: '/cities/' + id,
                data: updateCity,
                dataType: 'json',
                success: function(data) {
                    const [_, name, country] = getCellsInRow($(`tr[city-id=${data.id}]`))
                    $(name).text(data.name)
                    $(country).text(data.country)
                    closeModal()
                },
                error: function (err) {
                    const nameInput = $('#modal-content input[name="edit-name"]')
                    const countryInput = $('#modal-content input[name="edit-country"]')

                    $('.modal-edit-error').remove()

                    const errors = err.responseJSON.errors
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
            })
          })

          $('table').on('click', 'button.del-btn', function () {
            const [id, name, country] = getCellsInRow($(this).closest('tr'))
                .map(cell => $(cell).text())

            const content = `
                <p>Are you sure you want to delete ${name} (${country}) from the database?</p>
                <input type="hidden" name="_id" value="${id}">
            `
            const confirmBtn = `
                <button id="modal-delete-btn"
                    class="bg-red-500 hover:bg-indigo-300 text-white
                    font-semibold py-2 px-4 text-white rounded-xl mx-2"
                    >Confirm</button>
              `
            displayModal('Warning', content, confirmBtn, 'bg-red-500')
          })

          $('#modal').on('click', '#modal-delete-btn', function() {
            const modal = $('#modal')
            const id = $(modal).find('input[name="_id"]').val()

            $.ajax({
                type: 'DELETE',
                url: '/cities/' + id,
                dataType: 'json',
                success: function() {
                    $(`tr[city-id="${id}"]`).remove()
                    closeModal()
                },
                error: function (err) {
                    const content = parseErrorMessages(err)
                    displayModal('Edit Failed', content, '', 'bg-red-500')
                }
            })
          })
        })
      </script>
</x-layout>

