<x-layout>
    <div class="max-w-6xl m-auto mt-8">
        <div class="overflow-x-auto relative">
        <table class="w-full text-sm text-center">
            <caption style="display: none">Cities</caption>
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
                @foreach($cities as $city)
                    <tr class="bg-white border-b border-gray-100">
                        <td class="py-4 px-6">{{ $city->id }}</td>
                        <td class="py-4 px-6">{{ $city->name }}</td>
                        <td class="py-4 px-6">{{ $city->country }}</td>
                        <td class="py-4 px-6">{{ $city->flightsTo->count() }}</td>
                        <td class="py-4 px-6">{{ $city->flightsFrom->count() }}</td>
                        <td>
                            <x-button class="dark:bg-indigo-600 hover:bg-indigo-400">Edit</x-button>
                            <x-button class="ml-2 dark:bg-red-600 hover:bg-red-400">Delete</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

    </div>
</x-layout>

