@php
    $title = 'Airlines';
    $columnTitles = ['ID', 'Name', 'Description', 'Flights'];
@endphp

<x-layout :title="$title">

    <x-modal></x-modal>

    <div class="max-w-6xl m-auto mt-8">
        <div class="overflow-x-auto relative">
            <x-table
            :tableName="$title" :columnTitles="$columnTitles"
            :firstInput="'name'" :secondInput="'description'">

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

            </x-table>
        </div>
    </div>

    <div class="mt-12 mb-4 px-12">
        {{ $airlines->links() }}
    </div>

    <script>
        function displayModal(title, content, footer="", color="bg-indigo-500") {
            const modal = document.querySelector("#modal")

            let titleContainer = document.querySelector("#modal-title").closest("div")
            titleContainer.classList.remove("bg-indigo-500", "bg-red-500");
            titleContainer.classList.add(color)

            document.querySelector("#modal-title").textContent = title
            document.querySelector("#modal-content").innerHTML = content
            document.querySelector("#modal-footer").append(footer)

            modal.showModal()
        }

        function closeModal() {
            const modal = document.querySelector("#modal")
            const closeBtn = document.querySelector("#modal-close-btn")

            document.querySelector("#modal-title").textContent = ""
            document.querySelector("#modal-content").textContent = ""
            document.querySelector("#modal-footer").innerHTML = ""

            document.querySelector("#modal-footer").appendChild(closeBtn)
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
            const createRow = document.querySelector("tr#create-row")

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

        async function editAirline(target) {
            const id = document.querySelector('input[name="_id"]').value
            const newName = document.querySelector('input[name="edit-name"]')
            const newDesc = document.querySelector('input[name="edit-description"]')

            const response = await fetch(`/airlines/${id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: newName.value,
                    description: newDesc.value
                })
            })

            if (! response.ok) {
                const errors = (await response.json()).errors

                let previousErrors = document.querySelectorAll(".modal-edit-error")
                previousErrors.forEach(node => node.parentNode.removeChild(node))

                const para = document.createElement("p")
                para.setAttribute("class", "modal-edit-error text-red-500 text-xs mb-2")

                if (Object.hasOwn(errors, 'name')) {
                    para.textContent = errors.name.join(" ")
                    newName.after(para)
                }
                if (Object.hasOwn(errors, 'description')) {
                    para.textContent = errors.description.join(" ")
                    newDesc.after(para)
                }
                return
            }

            const row = document.querySelector(`tr[airline-id="${id}"]`)
            row.children[1].textContent = newName.value
            row.children[2].textContent = newDesc.value

            return closeModal()
        }

        function handleEditButton(target) {
            const id = target.closest("tr").firstElementChild.textContent
            const name = target.closest("tr").children[1].textContent
            const description = target.closest("tr").children[2].textContent

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

        const table = document.querySelector('table')
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
    </script>
</x-layout>

