@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-purple-400 text-start text-base font-medium text-white bg-gray-700/50 focus:outline-none focus:text-white focus:bg-gray-700/70 focus:border-purple-500 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700/50 hover:border-purple-400/50 focus:outline-none focus:text-white focus:bg-gray-700/50 focus:border-purple-400/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
