@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-bold mb-6">Edit User: {{ $user->name }}</h1>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Name</label>
                        <input type="text" name="name" id="name" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                        <input type="email" name="email" id="email" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password (Optional) -->
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2">Password (leave blank to keep current)</label>
                        <input type="password" name="password" id="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters (if changing)</p>
                        @error('password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium mb-2">Role</label>
                        <select name="role" id="role" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @if(auth()->id() === $user->id)
                            <p class="text-xs text-gray-500 mt-1">Cannot change your own role</p>
                        @endif
                        @error('role')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Info -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded text-sm">
                        <p><strong>Email Verified:</strong> {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y H:i') : 'Not verified' }}</p>
                        <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between items-center pt-6">
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                            ← Back
                        </a>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
