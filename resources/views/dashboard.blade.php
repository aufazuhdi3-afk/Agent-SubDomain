<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->isAdmin())
                <!-- Admin Dashboard -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-2xl font-bold mb-4">Admin Dashboard</h3>
                        <p class="mb-4">Welcome, {{ auth()->user()->name }}! You are logged in as an administrator.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-900 dark:text-blue-100">Pending Domains</h4>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-300">
                                    {{ \App\Models\Domain::where('status', 'pending')->count() }}
                                </p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-900 dark:text-green-100">Active Domains</h4>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-300">
                                    {{ \App\Models\Domain::where('status', 'active')->count() }}
                                </p>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                                <h4 class="font-semibold text-red-900 dark:text-red-100">Failed Domains</h4>
                                <p class="text-3xl font-bold text-red-600 dark:text-red-300">
                                    {{ \App\Models\Domain::where('status', 'failed')->count() }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('admin.domains.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-75 transition duration-150 ease-in-out">
                                Go to Domain Management
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- User Dashboard -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}!</h3>
                        <p class="mb-4">Manage your campus subdomains here.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-900 dark:text-blue-100">My Domains</h4>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-300">
                                    {{ auth()->user()->domains()->count() }} / 3
                                </p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-900 dark:text-green-100">Active</h4>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-300">
                                    {{ auth()->user()->domains()->where('status', 'active')->count() }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-4">
                            <a href="{{ route('domains.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-75 transition duration-150 ease-in-out">
                                View My Domains
                            </a>
                            <a href="{{ route('domains.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-75 transition duration-150 ease-in-out">
                                Request New Domain
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
