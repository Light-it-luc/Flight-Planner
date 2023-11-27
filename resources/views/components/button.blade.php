@props(['id'])

<button
    {{ $attributes->merge(['class' => 'text-white font-semibold py-2 px-4 text-white rounded-xl'])}}
    {{ isset($id) ? 'id=' . $id : '' }}
    {{ isset($type) ? 'type=' . $type : '' }}
>
    {{ $slot }}
</button>
