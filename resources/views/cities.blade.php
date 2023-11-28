<x-layout :title="'Cities'">

    <x-modal></x-modal>

    <div class="max-w-6xl m-auto mt-8">
        <div class="overflow-x-auto relative">
        <table class="w-full text-sm text-center">
            <caption class="hidden">Cities</caption>
            <thead class="text-xs text-gray-800 uppercase border-b border-gray-300">
            <tr>
                <th scope="col" class="py-3 px-6">ID</th>
                <th scope="col" class="py-3 px-6">City</th>
                <th scope="col" class="py-3 px-6">Country</th>
                <th scope="col" class="py-3 px-6">Incoming Flights</th>
                <th scope="col" class="py-3 px-6">Outgoing Flights</th>
            </tr>
            </thead>
            <tbody>
                <tr create-row class="bg-white border-b border-gray-100">
                    <td class="py-4 px-6"></td>
                    <td class="py-4 px-6">
                        <input
                            type="text"
                            name="name"
                            placeholder="Name"
                            required
                            class="p-1 text-center border border-gray-300 text-black
                            placeholder-gray-300 rounded-lg">
                    </td>
                    <td class="py-4 px-6">
                        <input
                            type="text"
                            name="country"
                            placeholder="Country"
                            required
                            class="p-1 text-center border border-gray-300 text-black
                            placeholder-gray-300 rounded-lg">
                    </td>
                    <td class="py-4 px-6"></td>
                    <td class="py-4 px-6"></td>
                    <td class="py-4 px-6">
                        <x-button
                            class="dark:bg-gray-500 hover:bg-gray-400"
                            create-button>
                            Create
                        </x-button>
                    </td>
                </tr>
                @foreach($cities as $city)
                    <tr city-id="{{ $city->id }}" class="bg-white border-b border-gray-100">
                        <td class="py-4 px-6">{{ $city->id }}</td>
                        <td class="py-4 px-6">{{ $city->name }}</td>
                        <td class="py-4 px-6">{{ $city->country }}</td>
                        <td class="py-4 px-6">{{ $city->flights_to_count }}</td>
                        <td class="py-4 px-6">{{ $city->flights_from_count }}</td>
                        <td class="py-4 px-6">
                            <x-button
                                      class="edit-btn dark:bg-indigo-600 hover:bg-indigo-400">
                                Edit
                            </x-button>
                            <x-button
                                      class="del-btn ml-2 dark:bg-red-600 hover:bg-red-400">
                                Delete
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-12 mb-4 px-12">
        {{ $cities->links() }}
    </div>

    <script>
        function displayModal(title, body, footer='') {
            var modal = document.getElementById('modal')

            $('[modal-title]').text(title)
            $('[modal-content]').html(body)
            $('[close-modal-btn]').before(footer)
            modal.showModal()
        }

        function closeModal() {
            var modal = document.getElementById('modal')
            var closeBtn = $('[close-modal-btn]')

            $('[modal-title]').text('')
            $('[modal-content]').html('')
            $('[modal-footer]').html(closeBtn)
            modal.close()
        }

        $(document).ready(function () {

          $('dialog').on('click', '[close-modal-btn]', closeModal)

          $('dialog').on('click', '[modal-submit-btn]', function(e) {
            var cityId = $(e.target).attr('city-id')
            var name = $(e.target).closest('#modal').find('input[name="name"]').val()
            var country = $(e.target).closest('#modal').find('input[name="country"]').val()

            var updateCity = {
                name: name.trim(),
                country: country.trim()
            }

            $.ajax({
                type: 'PATCH',
                url: 'http://localhost:80/cities/' + cityId,
                data: updateCity,
                dataType: 'json',
                headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    var updateRow = $(`tr[city-id="${data.id}"]`)
                    var cells = updateRow.children()
                    $(cells[1]).text(data.name)
                    $(cells[2]).text(data.country)
                    closeModal()
                },
                error: function (err) {
                    validationErrors = err.responseJSON.errors
                    content = ''

                    for(const failingField in validationErrors) {
                        var messages = validationErrors[failingField]
                        for (const msg of messages) {
                            content += `${msg}\n`
                          }
                      }
                    alert(content)
                }
            })
          })

          $('table').on('click', 'button[create-button]', function() {
              var city = {
                  name: $('input[name="name"]').val().trim(),
                  country: $('input[name="country"]').val().trim()
              }

              $.ajax({
                  type: 'POST',
                  url: '/cities',
                  data: city,
                  dataType: 'json',
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(data) {
                      const btnClass = 'text-white font-semibold py-2 px-4 text-white rounded-xl'

                      $('tr[create-row]').after(
                          `<tr city-id=${data.id} class="bg-white border-b border-gray-100">
                              <td class="py-4 px-6">${data.id}</td>
                              <td class="py-4 px-6">${data.name}</td>
                              <td class="py-4 px-6">${data.country}</td>
                              <td class="py-4 px-6">0</td>
                              <td class="py-4 px-6">0</td>
                              <td class="py-4 px-6">
                                  <button id="${data.id}"
                                          class="${btnClass} edit-btn dark:bg-indigo-600 hover:bg-indigo-400"
                                      >Edit</button>
                                  <button id="${data.id}"
                                          class="${btnClass} del-btn ml-2 dark:bg-red-600 hover:bg-red-400"
                                      >Delete</button>
                              </td>
                          </tr>`
                      )

                      $('input[name="name"]').val('')
                      $('input[name="country"]').val('')
                  },
                  error: function (err) {
                    const validationErrors = err.responseJSON.errors
                    let content = ''

                    for(const failingField in validationErrors) {
                        const messages = validationErrors[failingField]
                        content += messages.map(msg => `<li>${msg}</li>`).join('\n')
                      }
                      displayModal('Creation failed', `<ul>${content}</ul>`)
                  }
              })
          })

          $('table').on('click', 'button.edit-btn', function () {
              var currentRow = $(this).closest('tr')
              var cells = $(currentRow).children('td')

              var id = $(cells[0]).text()
              var name = $(cells[1]).text()
              var country = $(cells[2]).text()

              var content = `
                <div class="flex flex-column">
                    <div>
                        <div class="my-2">
                            <input
                                type="text"
                                name="name"
                                value="${name}"
                                required
                                class="p-1 text-center border border-gray-300 text-black
                                placeholder-gray-300 rounded-lg">
                        </div>
                        <div class="my-2">
                            <input
                                type="text"
                                name="country"
                                value="${country}"
                                required
                                class="p-1 text-center border border-gray-300 text-black
                                placeholder-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
              `
              var submitBtn = `
                <button modal-submit-btn city-id="${id}"
                    class="bg-indigo-500 hover:bg-indigo-300 text-white
                    font-semibold py-2 px-4 text-white rounded-xl mx-2"
                    >Submit</button>
              `


              displayModal('Edit City', content, submitBtn)
          })

        })
      </script>
</x-layout>

