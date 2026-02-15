<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-950 via-gray-900 to-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->isAdmin())
                <!-- Admin Dashboard -->
                <div class="mb-8">
                    <h3 class="text-3xl font-bold mb-2 text-white">👨‍💻 Admin Dashboard</h3>
                    <p class="text-gray-400 mb-8">Welcome, <span class="font-semibold text-purple-300">{{ auth()->user()->name }}</span>! You are logged in as an administrator.</p>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Pending Cards -->
                        <div class="bg-gray-800/50 backdrop-blur border border-yellow-500/20 rounded-2xl p-6 hover:border-yellow-500/40 transition group">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-yellow-200">⏳ Pending Domains</h4>
                                <svg class="w-8 h-8 text-yellow-500 opacity-20 group-hover:opacity-40 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-4xl font-bold text-yellow-400">{{ \App\Models\Domain::where('status', 'pending')->count() }}</p>
                            <p class="text-sm text-gray-400 mt-2">Awaiting review</p>
                        </div>

                        <!-- Active Cards -->
                        <div class="bg-gray-800/50 backdrop-blur border border-green-500/20 rounded-2xl p-6 hover:border-green-500/40 transition group">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-green-200">✅ Active Domains</h4>
                                <svg class="w-8 h-8 text-green-500 opacity-20 group-hover:opacity-40 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-4xl font-bold text-green-400">{{ \App\Models\Domain::where('status', 'active')->count() }}</p>
                            <p class="text-sm text-gray-400 mt-2">Ready to use</p>
                        </div>

                        <!-- Failed Cards -->
                        <div class="bg-gray-800/50 backdrop-blur border border-red-500/20 rounded-2xl p-6 hover:border-red-500/40 transition group">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-red-200">❌ Failed Domains</h4>
                                <svg class="w-8 h-8 text-red-500 opacity-20 group-hover:opacity-40 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-4xl font-bold text-red-400">{{ \App\Models\Domain::where('status', 'failed')->count() }}</p>
                            <p class="text-sm text-gray-400 mt-2">Need attention</p>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('admin.domains.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg hover:shadow-purple-500/50 rounded-lg font-semibold text-white transition transform hover:scale-105">
                        <span>📋 Manage All Domains</span>
                    </a>
                </div>
            @else
                <!-- User Dashboard -->
                <div class="mb-8">
                    <h3 class="text-3xl font-bold mb-2 text-white">👋 Welcome, {{ auth()->user()->name }}!</h3>
                    <p class="text-gray-400 mb-8">Manage your subdomains efficiently with Agent SubDomain.</p>
                    
                    <!-- User Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- My Domains Card -->
                        <div class="bg-gray-800/50 backdrop-blur border border-purple-500/20 rounded-2xl p-6 hover:border-purple-500/40 transition group">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-purple-200">📊 My Domains</h4>
                                <svg class="w-8 h-8 text-purple-500 opacity-20 group-hover:opacity-40 transition" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a4 4 0 014 4v9a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM12 9a1 1 0 100-2 1 1 0 000 2zm0 2a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-4xl font-bold text-purple-400">{{ auth()->user()->domains()->count() }} <span class="text-lg text-gray-400">/ 
                                @if(auth()->user()->hasUnlimitedSubdomains())
                                    ∞
                                @else
                                    {{ auth()->user()->getSubdomainLimit() }}
                                @endif
                            </span></p>
                            <p class="text-sm text-gray-400 mt-2">{{ auth()->user()->domains()->where('status', 'active')->count() }} active</p>
                        </div>

                        <!-- Quota Status Card -->
                        <div class="bg-gray-800/50 backdrop-blur border border-blue-500/20 rounded-2xl p-6 hover:border-blue-500/40 transition group">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-blue-200">📈 Quota Status</h4>
                                <svg class="w-8 h-8 text-blue-500 opacity-20 group-hover:opacity-40 transition" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" /></svg>
                            </div>
                            @if(auth()->user()->hasUnlimitedSubdomains())
                                <p class="text-4xl font-bold text-blue-400">∞ Unlimited</p>
                                <p class="text-sm text-gray-400 mt-2">No limits applied</p>
                            @else
                                <p class="text-4xl font-bold text-blue-400">{{ auth()->user()->getRemainingSlots() }} Remaining</p>
                                <div class="w-full bg-gray-700 rounded-full h-2 mt-3">
                                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-2 rounded-full transition-all" style="width: {{ (auth()->user()->domains()->count() / auth()->user()->getSubdomainLimit()) * 100 }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('domains.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:shadow-lg hover:shadow-blue-500/50 rounded-lg font-semibold text-white transition transform hover:scale-105">
                            <span>📋 View My Domains</span>
                        </a>
                        <a href="{{ route('domains.create') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:shadow-lg hover:shadow-green-500/50 rounded-lg font-semibold text-white transition transform hover:scale-105">
                            <span>➕ Request New Domain</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
