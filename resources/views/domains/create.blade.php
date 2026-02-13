@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-bold mb-6">Request New Domain</h2>

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 bg-red-100 text-red-700 border border-red-400 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('domains.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="subdomain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Subdomain <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center">
                            <input
                                type="text"
                                id="subdomain"
                                name="subdomain"
                                value="{{ old('subdomain') }}"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-l-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="e.g., my-campus"
                                pattern="^[a-z0-9-]+$"
                                maxlength="63"
                                required
                            />
                            <span class="px-4 py-2 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-md bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">
                                .unnar.id
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Only lowercase letters, numbers, and hyphens allowed. Maximum 63 characters.
                        </p>
                    </div>

                    <div>
                        <label for="target_ip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Target IP Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="target_ip"
                            name="target_ip"
                            value="{{ old('target_ip') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., 192.168.1.100"
                            required
                        />
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Enter a valid IPv4 or IPv6 address where your service is running.
                        </p>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-md p-4">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Note:</strong> Each account can request a maximum of 3 domains, with a limit of 3 requests per day. Your request will be reviewed by an administrator.
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-75 transition duration-150 ease-in-out"
                        >
                            Submit Request
                        </button>
                        <a
                            href="{{ route('domains.index') }}"
                            class="inline-flex items-center px-6 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 transition duration-150 ease-in-out"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
