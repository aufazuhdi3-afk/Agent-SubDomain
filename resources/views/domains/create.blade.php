@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800/50 backdrop-blur border border-purple-500/10 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 text-white">
                <h2 class="text-2xl font-bold mb-6">Request New Domain</h2>

                <!-- Limit Info -->
                <div class="mb-6 p-4 rounded-xl border-l-4 {{ auth()->user()->hasUnlimitedSubdomains() ? 'border-green-500 bg-green-900/20' : 'border-purple-500 bg-purple-900/20' }}">
                    <p class="text-sm font-semibold">
                        <strong>Your Subdomain Quota:</strong>
                        @if(auth()->user()->hasUnlimitedSubdomains())
                            <span class="text-green-400">∞ Unlimited</span>
                        @else
                            <span class="text-purple-400">
                                {{ auth()->user()->domains()->count() }}/{{ auth()->user()->subdomain_limit }} used
                                ({{ $remainingSlots }} remaining)
                            </span>
                        @endif
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 bg-red-900/30 text-red-400 border border-red-500/30 rounded-lg">
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
                        <label for="subdomain" class="block text-sm font-medium text-gray-200 mb-2">
                            Subdomain <span class="text-red-400">*</span>
                        </label>
                        <div class="flex items-center">
                            <input
                                type="text"
                                id="subdomain"
                                name="subdomain"
                                value="{{ old('subdomain') }}"
                                class="flex-1 px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-l-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500/50 focus:border-purple-500 text-white"
                                placeholder="e.g., my-campus"
                                pattern="^[a-z0-9-]+$"
                                maxlength="63"
                                required
                            />
                            <span class="px-4 py-2 border border-l-0 border-purple-500/20 rounded-r-lg bg-gray-700/30 text-gray-400">
                                .unnar.id
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-gray-400">
                            Only lowercase letters, numbers, and hyphens allowed. Maximum 63 characters.
                        </p>
                    </div>

                    <div>
                        <label for="target_ip" class="block text-sm font-medium text-gray-200 mb-2">
                            Target IP Address <span class="text-red-400">*</span>
                        </label>
                        <input
                            type="text"
                            id="target_ip"
                            name="target_ip"
                            value="{{ old('target_ip') }}"
                            class="w-full px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500/50 focus:border-purple-500 text-white"
                            placeholder="e.g., 192.168.1.100"
                            required
                        />
                        <p class="mt-2 text-sm text-gray-400">
                            Enter a valid IPv4 or IPv6 address where your service is running.
                        </p>
                    </div>

                    <div class="bg-purple-900/20 border border-purple-500/30 rounded-lg p-4">
                        <p class="text-sm text-purple-300">
                            <strong>Note:</strong> Each account can request a maximum of 3 domains, with a limit of 3 requests per day. Your request will be reviewed by an administrator.
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:from-purple-700 hover:to-pink-700 active:from-purple-800 active:to-pink-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-75 transition duration-150 ease-in-out"
                        >
                            Submit Request
                        </button>
                        <a
                            href="{{ route('domains.index') }}"
                            class="inline-flex items-center px-6 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg font-semibold text-sm text-gray-200 uppercase tracking-widest hover:bg-gray-700 hover:text-white transition duration-150 ease-in-out"
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
