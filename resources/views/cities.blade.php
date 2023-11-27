<x-layout :title="'Cities'">

    <div class="max-w-6xl m-auto mt-8">
        <div class="overflow-x-auto relative">
        <table class="w-full text-sm text-center">
            <caption class="hidden">Cities</caption>
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
                            name="country"
                            placeholder="Country"
                            required
                            class="p-1 text-center border border-gray-300 text-black
                            placeholder-gray-300 rounded-lg">
                    </td>
                    <td class="py-4 px-6"></td>
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
                @foreach($cities as $city)
                    <tr class="bg-white border-b border-gray-100">
                        <td class="py-4 px-6">{{ $city->id }}</td>
                        <td class="py-4 px-6">{{ $city->name }}</td>
                        <td class="py-4 px-6">{{ $city->country }}</td>
                        <td class="py-4 px-6">{{ $city->flights_to_count }}</td>
                        <td class="py-4 px-6">{{ $city->flights_from_count }}</td>
                        <td class="py-4 px-6">
                            <x-button id="{{ $city->id }}"
                                      class="edit-btn dark:bg-indigo-600 hover:bg-indigo-400">
                                Edit
                            </x-button>
                            <x-button id="{{ $city->id }}"
                                      class="del-btn ml-2 dark:bg-red-600 hover:bg-red-400">
                                Delete
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-12 mb-4 px-12">
        {{ $cities->links() }}
    </div>
</x-layout>
