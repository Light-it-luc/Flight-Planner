<div class="w-56 mb-8">
    <button class="w-full rounded-lg text-xl font-medium text-gray-700">Filter by City</button>
    <select id="city-filter" class="w-full mt-2 rounded-lg bg-gray-100">
        <option class="px-3 py-1 text-sm border-b border-gray-200" selected>All</option>
        @foreach ($cities as $city)
        <option
        class="px-3 py-1 text-sm {{ $loop->last ? '' : 'border-b border-gray-200' }}"
        city-id="{{ $city->id }}">
            {{ $city->name }}
        </option>
        @endforeach
    </select>
</div>
