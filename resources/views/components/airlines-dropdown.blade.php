<div class="w-56 mb-8">
    <button class="w-full rounded-lg text-xl font-medium text-gray-700">Filter by Airline</button>
    <select id="airline-filter" class="w-full mt-2 rounded-lg bg-gray-100">
        <option class="px-3 py-1 text-sm border-b border-gray-200" selected airline-id="0">All</option>
        @foreach ($airlines as $airline)
        <option
        class="px-3 py-1 text-sm {{ $loop->last ? '' : 'border-b border-gray-200' }}"
        airline-id="{{ $airline->id }}">
            {{ $airline->name }}
        </option>
        @endforeach
    </select>
</div>
