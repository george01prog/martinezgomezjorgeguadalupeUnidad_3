<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-700 antialiased">
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg">
                    <x-icon name="cube" class="h-8 w-8" />
                </span>
                <h1 class="mt-4 text-2xl font-bold text-white">{{ config('app.name') }}</h1>
                <p class="mt-1 text-sm text-slate-300">Gestión de productos y existencias</p>
            </div>

            <div class="rounded-2xl bg-white px-8 py-8 shadow-2xl">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>