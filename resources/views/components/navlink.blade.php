@props(['active'])

<a
    class="w-24 ml-2 py-2 px-4 text-center rounded-xl font-semibold text-lg
    {{ $active ? ' text-white bg-black' : ' text-gray-700 hover:text-black hover:bg-gray-200' ; }}"
    href="{{ $attributes['href'] }}">

    {{ $slot }}
</a>

