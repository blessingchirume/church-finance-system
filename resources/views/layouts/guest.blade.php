<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Foundation of Hope Finance Portal') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 pt-6 sm:pt-0 bg-slate-100">
            <div class="text-center">
                <a href="{{ route('login') }}" class="inline-flex flex-col items-center">
                    <img src="{{ asset('logo.jpg') }}" alt="Foundation of Hope" class="h-28 w-28 rounded-xl object-cover shadow-sm ring-1 ring-slate-200">
                    <span class="mt-4 text-sm font-bold uppercase tracking-wide text-emerald-700">Foundation of Hope</span>
                    <span class="mt-1 text-xs font-medium text-slate-500">Church Finance Administration</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white px-6 py-5 shadow-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
