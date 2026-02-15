<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Agent SubDomain') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            }
        </style>
    </head>
    <body class="font-sans text-white antialiased bg-gradient-to-br from-gray-950 via-gray-900 to-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>

            <div class="relative z-10">
                <a href="/" class="inline-flex items-center gap-2 mb-6">
                    <x-application-logo class="w-8 h-8 text-purple-400" />
                    <span class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Agent SubDomain</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-gray-800/50 backdrop-blur border border-purple-500/20 shadow-2xl overflow-hidden sm:rounded-2xl relative z-10">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-sm text-gray-400 relative z-10">
                <p>&copy; {{ date('Y') }} Agent SubDomain. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
