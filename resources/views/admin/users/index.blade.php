@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800/50 backdrop-blur border border-purple-500/10 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 text-white">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">User Management</h1>
                    <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-2 rounded-lg hover:from-purple-700 hover:to-pink-700 transition">
                        + Create User
                    </a>
                </div>

                @if ($message = Session::get('success'))
                    <div class="mb-4 p-4 bg-green-900/30 border border-green-500/30 text-green-400 rounded-lg">
                        {{ $message }}
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="mb-4 p-4 bg-red-900/30 border border-red-500/30 text-red-400 rounded-lg">
                        {{ $message }}
                    </div>
                @endif

                <!-- Search -->
                <div class="mb-6">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" placeholder="Search by name or email..." value="{{ $search }}" 
                            class="flex-1 px-3 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-purple-500/50">
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                            Search
                        </button>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-700/30 border-b border-purple-500/10">
                            <tr>
                                <th class="px-4 py-3 text-left text-purple-300 font-semibold">ID</th>
                                <th class="px-4 py-3 text-left text-purple-300 font-semibold">Name</th>
                                <th class="px-4 py-3 text-left text-purple-300 font-semibold">Email</th>
                                <th class="px-4 py-3 text-left text-purple-300 font-semibold">Role</th>
                                <th class="px-4 py-3 text-left text-purple-300 font-semibold">Subdomain Limit</th>
                                <th class="px-4 py-3 text-left text-purple-300 font-semibold">Verified</th>
                                <th class="px-4 py-3 text-left text-purple-300 font-semibold">Created</th>
                                <th class="px-4 py-3 text-center text-purple-300 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-500/10">
                            @forelse ($users as $user)
                                <tr class="border-b border-purple-500/10 hover:bg-gray-700/20 transition">
                                    <td class="px-4 py-3 text-gray-300">{{ $user->id }}</td>
                                    <td class="px-4 py-3 font-semibold text-white">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-300">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-white text-xs font-bold inline-block
                                            {{ $user->role === 'admin' ? 'bg-red-900/30 border border-red-500/30 text-red-400' : 'bg-purple-900/30 border border-purple-500/30 text-purple-400' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($user->hasUnlimitedSubdomains())
                                            <span class="text-green-400 font-semibold">∞ Unlimited</span>
                                        @else
                                            <span class="text-purple-400 font-semibold">{{ $user->subdomain_limit }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($user->email_verified_at)
                                            <span class="text-green-400 text-lg">✓</span>
                                        @else
                                            <span class="text-red-400 text-lg">✗</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                                class="text-purple-400 hover:text-purple-300 font-semibold transition">Edit</a>
                                            
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.toggleRole', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-yellow-400 hover:text-yellow-300 font-semibold transition"
                                                        onclick="return confirm('Change role to {{ $user->role === 'admin' ? 'User' : 'Admin' }}?')">
                                                        Toggle
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 font-semibold transition"
                                                        onclick="return confirm('Delete this user?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-400">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
