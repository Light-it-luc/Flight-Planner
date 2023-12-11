<x-layout :title="'Airlines'">

    <x-modal />

    <div class="max-w-6xl m-auto mt-8">
        <div class="overflow-x-auto relative">
            <x-table
            :tableName="'Airlines'" :columnTitles="['ID', 'Name', 'Description', 'Flights']"
            :firstInput="'name'" :secondInput="'description'">
            </x-table>
        </div>
    </div>

    <div id="pages-container" class="mt-12 mb-4 px-12"></div>

    <script>
        const displayModal = (title, body, footer='', color='bg-indigo-500') => {
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

        const clearPaginationLinks = () => {
            $('#pages-container button').remove()
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

        const clearAirlinesTable = () => {
            $('tr[airline-id]').remove()
        }

        const populateAirlinesTable = async (page=1) => {
            let queryParams = new URLSearchParams(window.location.search);

            const response = await fetch(`api/v1/airlines?${queryParams.toString()}`)

            if (! response.ok) {
                return displayModal("Error", "Something happened when trying to populate table", "", "bg-red-500")
            }

            const retrieved = await response.json()
            const airlines = retrieved.data

            clearAirlinesTable()
            airlines.forEach(airline => addRowInAirlinesTable(airline))
            regeneratePaginationLinks(retrieved.links)
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

        const addRowInAirlinesTable = (airline) => {
            const btnClass = 'text-white font-semibold py-2 px-4 text-white rounded-xl'

            $('tbody').append(
            `<tr airline-id=${airline.id} class="bg-white border-b border-gray-100">
                <td class="py-4 px-6">${airline.id}</td>
                <td class="py-4 px-6">${airline.name}</td>
                <td class="py-4 px-6">${airline.description}</td>
                <td class="py-4 px-6">0</td>
                <td class="py-4 px-6">
                <div id="btn-container" class="flex flex-row">
                    <button id="${airline.id}"
                            class="${btnClass} edit-btn dark:bg-indigo-600 hover:bg-indigo-400"
                        >Edit</button>
                    <button id="${airline.id}"
                            class="${btnClass} del-btn ml-2 dark:bg-red-600 hover:bg-red-400"
                        >Delete</button>
                </div>
                </td>
            </tr>`
            )
        }

        const makePost = async (url, data) => {
            return await fetch(url, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(data)
            })
        }

        const makePut = async (url, data) => {
            return await fetch(url, {
                method: "PUT",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(data)
            })
        }

        const makeDelete = async (url) => {
            return await fetch(url, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                }
            })
        }

        const parseErrors = (err) => {
            const validationErrors = err.errors
            let content = ''

            messages = Object.values(validationErrors).flat()
            messages.map(err => content += `<li>${err}</li>`)

            return `<ul>${content}</ul>`
        }

        const getAirlineFromInputs = (name, description) => {
            const [inputName, inputDesc] = getInputValues(name, description)

            const airline = {
                name: inputName.trim(),
                description: inputDesc.trim()
            }

            return airline
        }

        const resetInputField = (name, value="") => {
            $(`input[name="${name}"]`).val(value)
        }

        const handleEditErrors = (errors) => {
            const nameInput = $('input[name="edit-name"]')
            const descInput = $('input[name="edit-description"]')

            $('.modal-edit-error').remove()

            if (Object.hasOwn(errors, 'name')) {
            let nameErrors =
                `<p class="modal-edit-error text-xs text-red-500 mb-3">${errors.name.join(" ")}</p>`
                $(nameInput).after(nameErrors)
            }
            if (Object.hasOwn(errors, 'description')) {
                let descErrors =
                `<p class="modal-edit-error text-xs text-red-500 mb-3">${errors.description.join(" ")}</p>`
                $(descInput).after(descErrors)
            }
        }

        const createAirline = async () => {
            const newAirline = getAirlineFromInputs("name", "description")

            const response = await makePost("api/v1/airlines", newAirline)

            if (! response.ok) {
                const errors = await response.json()
                const content = parseErrors(errors)
                return displayModal("Creation Failed", content, "", "bg-red-500")
            }

            resetInputField("name")
            resetInputField("description")

            populateAirlinesTable()
        }

        const deleteAirline = async (id) => {
            const response = await makeDelete(`api/v1/airlines/${id}`)

            if (! response.ok) {
                return displayModal(
                    "Warning", "An error occurred while trying to delete", "", "bg-red-500"
                )
            }

            populateAirlinesTable()
            closeModal()
        }

        const editAirline = async (target) => {
            const [id] = getInputValues("_id")
            const airline = getAirlineFromInputs("edit-name", "edit-description")

            const response = await makePut(`api/v1/airlines/${id}`, airline)

            if (! response.ok) {
                const errors = (await response.json()).errors
                return handleEditErrors(errors)
            }

            populateAirlinesTable()

            return closeModal()
        }

        const handleDeleteButton = (target) => {
            const id = target.closest("tr").firstElementChild.textContent
            const name = target.closest("tr").children[1].textContent

            const deleteBtn = `
                <button id="modal-delete-btn"
                    class="bg-red-500 hover:bg-red-300 text-white
                    font-semibold py-2 px-4 text-white rounded-xl mx-2"
                    >Confirm</button>
              `

            const warningMessage = `
            <input type="hidden" name="_id" value="${id}">
            <p>Are you sure you want to delete ${name}?</p>
            `

            displayModal("Warning", warningMessage, deleteBtn, "bg-red-500")
        }

        const handleEditButton = (target) => {
            const currentRow = target.closest("tr")

            const id = currentRow.firstElementChild.textContent
            const name = currentRow.children[1].textContent
            const description = currentRow.children[2].textContent

            const inputs = `
            <input type="hidden" name="_id" value="${id}">
            <label class="block mb-1 uppercase font-bold text-sm text-gray-700" for="edit-name">
                Name
            </label>
            <input type="text" name="edit-name" value="${name}"
            class="mb-2 p-1 text-center border border-gray-300 text-black placeholder-gray-300 rounded-lg">
            <label class="block mb-1 uppercase font-bold text-sm text-gray-700" for="edit-description">
                Description
            </label>
            <input type="text" name="edit-description" value="${description}"
            class="mb-2 p-1 text-center border border-gray-300 text-black placeholder-gray-300 rounded-lg">
            `

            const updateBtn = document.createElement("button")
            updateBtn.setAttribute("id", "modal-edit-btn")
            updateBtn.setAttribute(
                "class",
                "bg-indigo-500 hover:bg-indigo-300 text-white font-semibold y-2 px-4 text-white rounded-xl mx-2"
            )
            updateBtn.textContent = "Update"

            return displayModal("Edit Airline", inputs, updateBtn)
        }

        $(document).ready(() => {

            populateAirlinesTable()

            $("table").on("click", ".edit-btn", (event) => handleEditButton(event.target))

            $("table").on("click", ".del-btn", (event) => handleDeleteButton(event.target))

            $("#create-button").on("click", createAirline)

            $("#modal").on("click", "#modal-close-btn", closeModal)

            $("#modal").on("click", "#modal-delete-btn", () => {
                const airlineId = $(`dialog input[name="_id"]`).val()
                return deleteAirline(airlineId)
            })

            $("#modal").on("click", "#modal-edit-btn", (event) => editAirline(event.target))

            $('#pages-container').on("click", ".page-btn", (event) => {
                const queryParams = $(event.target)
                .attr("url")
                .split("?")[1]

                history.pushState(null, "", "airlines?" + queryParams)
                populateCitiesTable()
          })
        })
    </script>
</x-layout>

