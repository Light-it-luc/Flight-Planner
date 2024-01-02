@props(['tableName', 'columnTitles'])

<table class="w-full text-sm text-center">
    <caption class="hidden">{{ $tableName }}</caption>
    <thead class="text-xs text-gray-800 uppercase border-b border-gray-300">
    <tr>
        @foreach ($columnTitles as $title)
            <th id="col-{{ strtolower($title) }}" scope="col" class="py-3 px-6 rounded-lg">{{ $title }}</th>
        @endforeach
            {{-- Additional column for edit/delete buttons --}}
            <th scope="col" class="py-3 px-6 rounded-lg"></th>
    </tr>
    </thead>
    <tbody>

        {{ $slot }}

    </tbody>
</table>
