<nav class="bg-white">
    <div class="border py-3 px-4">
        <div class="flex justify-between">
            <div class="p-2 border border-transparent rounded-xl hover:border-gray-200">
                <a href="flights" class="flex items-center">
                    <!-- Logo -->
                    <img src="/images/plane.png" alt="Logo" width="24" height="24">
                    <span class="ml-2 font-semibold text-lg text-gray-700">Flight Planner</span>
                </a>
            </div>

            <div class="flex items-center">
                <x-navlink href="cities">Cities</x-navlink>
                <x-navlink href="airlies">Airlines</x-navlink>
                <x-navlink href="flights">Flights</x-navlink>
            </div>

            <div class="ml-2 flex">
                <div class="flex cursor-pointer items-center gap-x-1 rounded-xl py-2 px-4 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5 text-gray-500"
                         viewBox="0 0 20 20"
                         fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0
                            115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                          clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">Favorites</span>
                </div>

                <div class="ml-2 flex cursor-pointer items-center gap-x-1 rounded-xl
                            border py-2 px-4 hover:bg-gray-100">
                    <span class="text-sm font-medium">Sign in</span>
                </div>
            </div>
        </div>
    </div>
</nav>
