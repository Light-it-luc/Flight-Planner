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
        const displayModal = (title, content, footer="", color="bg-indigo-500") => {
            const modal = document.querySelector("#modal")

            let titleContainer = document.querySelector("#modal-title").closest("div")
            titleContainer.classList.remove("bg-indigo-500", "bg-red-500");
            titleContainer.classList.add(color)

            document.querySelector("#modal-title").textContent = title
            document.querySelector("#modal-content").innerHTML = content
            document.querySelector("#modal-footer").append(footer)

            modal.showModal()
        }

        const closeModal =  () => {
            const modal = document.querySelector("#modal")
            const closeBtn = document.querySelector("#modal-close-btn")

            document.querySelector("#modal-title").textContent = ""
            document.querySelector("#modal-content").textContent = ""
            document.querySelector("#modal-footer").innerHTML = ""

            document.querySelector("#modal-footer").appendChild(closeBtn)
            modal.close()
        }

        const clearPaginationLinks = () => {
            const container = document.querySelector("#pages-container")
            const links = document.querySelectorAll("#pages-container button")

            links.forEach(link => container.removeChild(link))
        }

        const regeneratePaginationLinks = (links) => {
            clearPaginationLinks()
            const btnContainer = document.querySelector("#pages-container")

            links.forEach(link => {
                const btn = document.createElement("button")
                btn.setAttribute("class", "text-black p-1 border border-gray-500 min-w-fit px-4 rounded-lg m-2")

                if (link.url) {
                    btn.classList.add("page-btn")
                    btn.setAttribute("url", link.url)
                }

                let backgroundColor = (link.active) ? "bg-gray-300": "bg-white";
                btn.classList.add(backgroundColor)

                btn.textContent = link.label.replace("&laquo;", "").replace("&raquo;", "")
                btnContainer.appendChild(btn)
            })
        }

        const clearAirlinesTable = () => {
            const tbody = document.querySelector('tbody')
            let rows = document.querySelectorAll('tr[airline-id]')

            rows.forEach(row => tbody.removeChild(row))
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
                const node = document.querySelector(`input[name="${name}"]`)
                if (node) {
                    values.push(node.value)
                }
            }

            return values
        }

        const addRowInAirlinesTable = (airline) => {
            const btnClass = 'text-white font-semibold py-2 px-4 text-white rounded-xl'

            const content = `
            <tr class="bg-white border-b border-gray-100">
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
            </tr>
            `

            const row = document.createElement('tr')
            row.innerHTML = content
            row.setAttribute("airline-id", airline.id)

            document.querySelector("tbody").append(row)
        }

        const makePost = async (url, data) => {
            return await fetch(url, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
        }

        const makeDelete = async (url) => {
            return await fetch(url, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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
            const inputName = document.querySelector(`input[name="${name}"]`)
            inputName.value = value
        }

        const handleErrorsInEditModal = (errors) => {
            let previousErrors = document.querySelectorAll(".modal-edit-error")
            previousErrors.forEach(node => node.parentNode.removeChild(node))

            if (Object.hasOwn(errors, 'name')) {
                const nameErrors = document.createElement("p")
                nameErrors.setAttribute("class", "modal-edit-error text-red-500 text-xs mb-2")
                nameErrors.textContent = errors.name.join(" ")
                document.querySelector('input[name="edit-name"]').after(nameErrors)
            }
            if (Object.hasOwn(errors, 'description')) {
                const descErrors = document.createElement("p")
                descErrors.setAttribute("class", "modal-edit-error text-red-500 text-xs mb-2")
                descErrors.textContent = errors.description.join(" ")
                document.querySelector('input[name="edit-description"]').after(descErrors)
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
                return handleErrorsInEditModal(errors)
            }

            populateAirlinesTable()

            return closeModal()
        }

        const handleDeleteButton = (target) => {
            const id = target.closest("tr").firstElementChild.textContent
            const name = target.closest("tr").children[1].textContent
            const deleteBtn = document.createElement("button")
            deleteBtn.textContent = "Delete"
            deleteBtn.setAttribute(
                "class",
                "bg-red-500 hover:bg-red-300 text-white font-semibold py-2 px-4 text-white rounded-xl mx-2"
            )
            deleteBtn.setAttribute("id", "modal-delete-btn")

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

        populateAirlinesTable()

        const table = document.querySelector("table")
        table.addEventListener("click", function (event) {
            const target = event.target
            if (target.classList.contains("edit-btn")) {
                return handleEditButton(target)
            }
            if (target.classList.contains("del-btn")) {
                return handleDeleteButton(target)
            }
        })

        const createBtn = document.querySelector('#create-button')
        createBtn.addEventListener("click", createAirline)

        const closeModalBtn = document.querySelector("#modal-close-btn")
        closeModalBtn.addEventListener("click", closeModal)

        const modal = document.querySelector("#modal")
        modal.addEventListener("click", function (event) {
            const targetId = event.target.getAttribute("id")

            if (targetId === "modal-delete-btn") {
                const airlineId = document.querySelector(`dialog input[name="_id"]`).value
                return deleteAirline(airlineId)
            }

            if (targetId === "modal-edit-btn") {
                return editAirline(event.target)
            }
        })

        const pageContainer = document.querySelector("#pages-container")
        pageContainer.addEventListener("click", (event) => {
            if (event.target.classList.contains("page-btn")) {
                const queryParams = $(event.target)
                  .attr("url")
                  .split("?")[1]

                history.pushState(null, "", "airlines?" + queryParams)
                populateAirlinesTable()
            }
        })
    </script>
</x-layout>

