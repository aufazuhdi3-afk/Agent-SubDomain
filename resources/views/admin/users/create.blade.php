@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-bold mb-6">Create New User</h1>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Name</label>
                        <input type="text" name="name" id="name" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                        <input type="email" name="email" id="email" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2">Password</label>
                        <input type="password" name="password" id="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            required>
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                        @error('password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                            required>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium mb-2">Role</label>
                        <select name="role" id="role" 
                            class="w-full px-4 py-2 border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                            <option value="user" selected>User</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between items-center pt-6">
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                            ← Back
                        </a>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
