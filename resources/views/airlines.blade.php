<x-layout :title="'Airlines'">

    <x-modal :id="'modal'"></x-modal>

    <div class="max-w-6xl m-auto mt-8">
        <div class="overflow-x-auto relative">
        <table class="w-full text-sm text-center">
            <caption class="hidden">Airlines</caption>
            <thead class="text-xs text-gray-800 uppercase border-b border-gray-300">
            <tr>
                <th scope="col" class="py-3 px-6">ID</th>
                <th scope="col" class="py-3 px-6">Airline Name</th>
                <th scope="col" class="py-3 px-6">Description</th>
                <th scope="col" class="py-3 px-6">Flights</th>
                <th scope="col" class="py-3 px-6"></th>
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
                            name="description"
                            placeholder="Description"
                            required
                            class="p-1 text-center border border-gray-300 text-black
                            placeholder-gray-300 rounded-lg">
                    </td>
                    <td class="py-4 px-6"></td>
                    <td class="py-4 px-6">
                        <x-button
                            type="button"
                            class="dark:bg-gray-500 hover:bg-gray-400"
                            create-button>
                            Create
                        </x-button>
                    </td>
                </tr>
                @foreach($airlines as $airline)
                    <tr airline-id="{{ $airline->id }}" class="bg-white border-b border-gray-100">
                        <td class="py-4 px-6">{{ $airline->id }}</td>
                        <td class="py-4 px-6">{{ $airline->name }}</td>
                        <td class="py-4 px-6">{{ $airline->description }}</td>
                        <td class="py-4 px-6">{{ $airline->flights_count }}</td>
                        <td class="py-4 px-6">
                            <div id="btn-container" class="flex flex-row">
                                <x-button
                                    class="edit-btn m-1 w-3/5 dark:bg-indigo-600 hover:bg-indigo-400">Edit</x-button>
                                <x-button
                                    class="del-btn m-1 w-3/5 dark:bg-red-600 hover:bg-red-400">Delete</x-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-12 mb-4 px-12">
        {{ $airlines->links() }}
    </div>

    <script>
        function displayModal(title, content, footer="", color="bg-indigo-500") {
            const modal = document.querySelector("#modal")

            let titleContainer = document.querySelector("[modal-title]").closest("div")
            titleContainer.classList.add(color)

            document.querySelector("[modal-title]").textContent = title
            document.querySelector("[modal-content]").innerHTML = content
            document.querySelector("[modal-footer]").append(footer)

            modal.showModal()
        }

        function closeModal() {
            const modal = document.querySelector("#modal")
            const closeBtn = document.querySelector("[close-modal-btn]")

            document.querySelector("[modal-title]").textContent = ""
            document.querySelector("[modal-content]").textContent = ""
            document.querySelector("[modal-footer]").innerHTML = ""

            document.querySelector("[modal-footer]").appendChild(closeBtn)
            modal.close()
        }

        function parseErrors(err) {
            const validationErrors = err.errors
            let content = ''

            messages = Object.values(validationErrors).flat()
            messages.map(err => content += `<li>${err}</li>`)

            return `<ul>${content}</ul>`
        }

        function insertAirlineRow(airline) {
            let newRow = document.createElement("tr")
            newRow.setAttribute("airline-id", airline.id)
            const createRow = document.querySelector("tr[create-row]")

            newRow.setAttribute("class", createRow.getAttribute("class"))

            const targetProperties = ["id", "name", "description"]

            targetProperties.map((prop) => newRow.appendChild(createCell(airline[prop])))

            newRow.appendChild(createCell(0))

            newRow.appendChild(createEditDeleteBtns(airline.id))
            createRow.after(newRow)
        }

        function createCell(content) {
            const cellClass = document.querySelector("td").getAttribute("class")

            let newCell = document.createElement("td")
            newCell.setAttribute("class", cellClass)
            newCell.textContent = content

            return newCell
        }

        function createEditDeleteBtns(id) {
            const cell = createCell("")
            const container = document.createElement("div")
            container.setAttribute("class", "flex flex-row")
            container.setAttribute("id", "btn-container")

            const editBtn = document.createElement("button")
            editBtn.textContent = "Edit"
            editBtn.setAttribute(
                "class",
                `edit-btn m-1 w-3/5 dark:bg-indigo-600 hover:bg-indigo-400
                text-white font-semibold py-2 px-4 text-white rounded-xl`
            )

            const deleteBtn = document.createElement("button")
            deleteBtn.textContent = "Delete"
            deleteBtn.setAttribute(
                "class",
                `del-btn m-1 w-3/5 dark:bg-red-600 hover:bg-red-400
                text-white font-semibold py-2 px-4 text-white rounded-xl`
            )

            container.append(editBtn)
            container.append(deleteBtn)

            cell.append(container)

            return cell
        }

        async function createAirline() {
            const name = document.querySelector('input[name="name"]')
            const description = document.querySelector('input[name="description"]')

            const newAirline = {
                name: name.value.trim(),
                description: description.value.trim()
            }

            const response = await fetch("/airlines", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(newAirline)
            })

            if (! response.ok) {
                const errors = await response.json()
                const content = parseErrors(errors)
                return displayModal("Creation Failed", content, "", "bg-red-500")
            }

            name.value = ""
            description.value = ""

            const storedAirline = await response.json()
            return insertAirlineRow(storedAirline)
        }

        async function deleteAirline(id) {
            const response = await fetch(`/airlines/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            })

            if (! response.ok) {
                return displayModal(
                    "Warning", "An error occurred while trying to delete", "", "bg-red-500"
                )
            }

            document.querySelector(`tr[airline-id="${id}"]`).remove()
            closeModal()
        }

        function handleDeleteButton(target) {
            const id = target.closest("tr").firstElementChild.textContent
            const name = target.closest("tr").children[1].textContent
            const deleteBtn = document.createElement("button")
            deleteBtn.textContent = "Delete"
            deleteBtn.setAttribute(
                "class",
                "bg-red-500 hover:bg-indigo-300 text-white font-semibold py-2 px-4 text-white rounded-xl mx-2"
            )
            deleteBtn.setAttribute("id", "modal-delete-btn")

            const warningMessage = `
            <input type="hidden" name="_id" value="${id}">
            <p>Are you sure you want to delete ${name}?</p>
            `

            displayModal("Warning", warningMessage, deleteBtn, "bg-red-500")
        }

        const table = document.querySelector('table')
        table.addEventListener("click", function (event) {
            const target = event.target
            if (target.classList.contains("edit-btn")) {
                // return handleEditButton(target)
            }
            if (target.classList.contains("del-btn")) {
                return handleDeleteButton(target)
            }
        })

        const createBtn = document.querySelector('[create-button]')
        createBtn.addEventListener("click", createAirline)

        const closeModalBtn = document.querySelector("[close-modal-btn]")
        closeModalBtn.addEventListener("click", closeModal)

        const modal = document.querySelector("#modal")
        modal.addEventListener("click", function (event) {
            const targetId = event.target.getAttribute("id")

            if (targetId === "modal-delete-btn") {
                const airlineId = document.querySelector(`dialog input[name="_id"]`).value
                return deleteAirline(airlineId)
            }
        })
    </script>
</x-layout>

