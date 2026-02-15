@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">User Management</h1>
                    <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        + Create User
                    </a>
                </div>

                @if ($message = Session::get('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ $message }}
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ $message }}
                    </div>
                @endif

                <!-- Search -->
                <div class="mb-6">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" placeholder="Search by name or email..." value="{{ $search }}" 
                            class="flex-1 px-3 py-2 border border-gray-300 rounded">
                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Search
                        </button>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Role</th>
                                <th class="px-4 py-2 text-left">Verified</th>
                                <th class="px-4 py-2 text-left">Created</th>
                                <th class="px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2">{{ $user->id }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ $user->name }}</td>
                                    <td class="px-4 py-2">{{ $user->email }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 rounded text-white text-xs font-bold
                                            {{ $user->role === 'admin' ? 'bg-red-600' : 'bg-blue-600' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        @if ($user->email_verified_at)
                                            <span class="text-green-600">✓</span>
                                        @else
                                            <span class="text-red-600">✗</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                                class="text-blue-600 hover:text-blue-800 font-semibold">Edit</a>
                                            
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.toggleRole', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 font-semibold"
                                                        onclick="return confirm('Change role to {{ $user->role === 'admin' ? 'User' : 'Admin' }}?')">
                                                        Toggle
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold"
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
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">
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
