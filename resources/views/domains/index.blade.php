@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800/50 backdrop-blur border border-purple-500/10 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 text-white">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">My Domains</h2>
                    <a href="{{ route('domains.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-75 transition duration-150 ease-in-out">
                        Request New Domain
                    </a>
                </div>

                @if ($message = session('success'))
                    <div class="mb-4 px-4 py-2 bg-green-900/30 text-green-400 border border-green-500/30 rounded-lg">
                        {{ $message }}
                    </div>
                @endif

                @if ($message = session('error'))
                    <div class="mb-4 px-4 py-2 bg-red-900/30 text-red-400 border border-red-500/30 rounded-lg">
                        {{ $message }}
                    </div>
                @endif

                @if ($domains->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-purple-500/10">
                            <thead class="bg-gray-700/30">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Domain</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Subdomain</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Target IP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800/30 divide-y divide-purple-500/10">
                                @foreach ($domains as $domain)
                                    <tr class="hover:bg-gray-700/20 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                            {{ $domain->full_domain }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $domain->subdomain }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $domain->target_ip }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($domain->status === 'active')
                                                    bg-green-900/30 text-green-400 border border-green-500/30
                                                @elseif($domain->status === 'pending')
                                                    bg-yellow-900/30 text-yellow-400 border border-yellow-500/30
                                                @elseif($domain->status === 'approved' || $domain->status === 'provisioning')
                                                    bg-purple-900/30 text-purple-400 border border-purple-500/30
                                                @else
                                                    bg-red-900/30 text-red-400 border border-red-500/30
                                                @endif
                                            ">
                                                {{ ucfirst($domain->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $domain->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if(in_array($domain->status, ['pending', 'failed']))
                                                <form method="POST" action="{{ route('domains.destroy', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 transition" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $domains->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-400 mb-4 text-lg">You haven't requested any domains yet.</p>
                        <a href="{{ route('domains.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-purple-700 hover:to-pink-700 transition">
                            Request Your First Domain
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
