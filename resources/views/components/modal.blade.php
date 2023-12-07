
<dialog id="modal" class="w-1/4 border rounded-lg">
    <div>
        <!-- Modal Header -->
        <div class="text-white px-4 py-2 flex justify-between rounded-lg bg-indigo-500">
            <h2 id="modal-title" class="text-lg font-semibold"></h2>
        </div>

        <!-- Modal Body -->
        <div id="modal-content" class="p-4 py-4 mx-4 border-b">
            {{ $slot }}
        </div>

        <!-- Modal Footer -->
        <div id="modal-footer" class="px-4 py-2 flex justify-end">
            <button
            id="modal-close-btn"
            class="bg-gray-600 hover:bg-gray-400 text-white font-semibold py-2 px-4 text-white rounded-xl w-1/4"
            >Close</button>
        </div>
    </div>
</dialog>
