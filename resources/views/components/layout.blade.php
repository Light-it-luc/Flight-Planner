@props(['title'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="{{ asset('/js/functions.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.js') }}"></script>
    <script src="{{ asset('js/axios/axios.min.js') }}"></script>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])

    <title>{{ isset($title) ? $title : 'Flight Planner'; }}</title>
</head>

<body>
    <x-navbar :title="$title"/>
    <img class="w-full h-96 object-cover" src="images/travel-plane.jpg" alt="Ready for your next trip?">

    {{ $slot }}
</body>
</html>
