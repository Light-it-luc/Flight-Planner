@props(['tableName', 'columnTitles', 'firstInput', 'secondInput'])

<table class="w-full text-sm text-center">
    <caption class="hidden">{{ $tableName }}</caption>
    <thead class="text-xs text-gray-800 uppercase border-b border-gray-300">
    <tr>
        @foreach ($columnTitles as $title)
            <th id="col-{{ strtolower($title) }}" scope="col" class="py-3 px-6">{{ $title }}</th>
        @endforeach
            {{-- Additional column for edit/delete buttons --}}
            <th scope="col" class="py-3 px-6"></th>
    </tr>
    </thead>
    <tbody>
        <tr id="create-row" class="bg-white border-b border-gray-100">
            <td class="py-4 px-6"></td>
            <td class="py-4 px-6">
                <x-input :name="$firstInput"></x-input>
            </td>
            <td class="py-4 px-6">
                <x-input :name="$secondInput"></x-input>
            </td>
            <td class="py-4 px-6"></td>
            <td class="py-4 px-6">
                <x-button id="create-button" class="dark:bg-gray-500 hover:bg-gray-400">Create</x-button>
            </td>
        </tr>

        {{ $slot }}

    </tbody>
</table>
