@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800/50 backdrop-blur border border-purple-500/10 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 text-white">
                <h1 class="text-2xl font-bold mb-6">Edit User: {{ $user->name }}</h1>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-900/30 border border-red-500/30 text-red-400 rounded-lg">
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
                        <label for="name" class="block text-sm font-medium mb-2 text-gray-200">Name</label>
                        <input type="text" name="name" id="name" 
                            class="w-full px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-purple-500/50"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2 text-gray-200">Email Address</label>
                        <input type="email" name="email" id="email" 
                            class="w-full px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-purple-500/50"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password (Optional) -->
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2 text-gray-200">Password (leave blank to keep current)</label>
                        <input type="password" name="password" id="password" 
                            class="w-full px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-purple-500/50">
                        <p class="text-xs text-gray-400 mt-1">Minimum 8 characters (if changing)</p>
                        @error('password')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2 text-gray-200">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="w-full px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-purple-500/50">
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium mb-2 text-gray-200">Role</label>
                        <select name="role" id="role" 
                            class="w-full px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg text-white focus:outline-none focus:ring-purple-500/50"
                            {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @if(auth()->id() === $user->id)
                            <p class="text-xs text-gray-400 mt-1">Cannot change your own role</p>
                        @endif
                        @error('role')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Subdomain Limit -->
                    <div>
                        <label for="subdomain_limit" class="block text-sm font-medium mb-2 text-gray-200">Subdomain Limit</label>
                        <div class="flex gap-2">
                            <input type="number" name="subdomain_limit" id="subdomain_limit" 
                                class="flex-1 px-4 py-2 bg-gray-700/50 border border-purple-500/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-purple-500/50"
                                placeholder="e.g., 3, 5, 10" min="3" value="{{ old('subdomain_limit', $user->subdomain_limit) }}">
                            <button type="button" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition font-semibold"
                                onclick="document.getElementById('subdomain_limit').value=''">
                                ∞ Unlimited
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Leave empty for unlimited, or enter number (minimum 3)</p>
                        @error('subdomain_limit')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Info -->
                    <div class="bg-gray-700/30 border border-purple-500/10 p-4 rounded-lg text-sm space-y-2 text-gray-300">
                        <p><strong>Email Verified:</strong> {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y H:i') : 'Not verified' }}</p>
                        <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
                        <hr class="border-purple-500/20 my-2">
                        <p><strong>Subdomains Created:</strong> {{ $user->domains()->count() }}</p>
                        <p>
                            <strong>Subdomain Limit:</strong>
                            @if($user->hasUnlimitedSubdomains())
                                <span class="text-green-400 font-semibold">∞ Unlimited</span>
                            @else
                                <span class="text-purple-400 font-semibold">{{ $user->subdomain_limit }} ({{ $user->domains()->count() }}/{{ $user->subdomain_limit }} used)</span>
                            @endif
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between items-center pt-6">
                        <a href="{{ route('admin.users.index') }}" class="text-purple-400 hover:text-purple-300 transition">
                            ← Back
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-2 rounded-lg transition font-semibold">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
