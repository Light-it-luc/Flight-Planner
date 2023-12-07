@props(['name', 'type', 'value'])

<input
    type="{{ isset($type)? $type: 'text'; }}"
    name="{{ strtolower($name) }}"
    placeholder="{{ ucfirst($name) }}"
    {{ isset($value)? 'value="' . $value . '"' : ''}}
    required
    class="p-1 text-center border border-gray-300 text-black
    placeholder-gray-300 rounded-lg">
