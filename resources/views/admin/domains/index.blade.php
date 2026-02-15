@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800/50 backdrop-blur border border-purple-500/10 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 text-white">
                <h2 class="text-2xl font-bold mb-6">Domain Management</h2>

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

                <!-- Filter by Status -->
                <div class="mb-6 flex gap-2 flex-wrap">
                    <a
                        href="{{ route('admin.domains.index') }}"
                        class="inline-flex items-center px-4 py-2 border rounded-lg font-semibold text-xs uppercase tracking-widest transition duration-150 ease-in-out 
                        {{ is_null($status) ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white border-transparent' : 'bg-gray-700/50 text-gray-300 border-purple-500/20 hover:text-white' }}"
                    >
                        All
                    </a>
                    @foreach(['pending', 'approved', 'provisioning', 'active', 'failed', 'suspended'] as $s)
                        <a
                            href="{{ route('admin.domains.index', ['status' => $s]) }}"
                            class="inline-flex items-center px-4 py-2 border rounded-lg font-semibold text-xs uppercase tracking-widest transition duration-150 ease-in-out 
                            {{ $status === $s ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white border-transparent' : 'bg-gray-700/50 text-gray-300 border-purple-500/20 hover:text-white' }}"
                        >
                            {{ ucfirst($s) }}
                        </a>
                    @endforeach
                </div>

                @if ($domains->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-purple-500/10">
                            <thead class="bg-gray-700/30">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Domain</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Target IP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Requested</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800/30 divide-y divide-purple-500/10">
                                @foreach ($domains as $domain)
                                    <tr class="hover:bg-gray-700/20 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ $domain->user->name }} <br>
                                            <span class="text-xs text-gray-400">{{ $domain->user->email }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                            {{ $domain->full_domain }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $domain->target_ip ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border
                                                @if($domain->status === 'active')
                                                    bg-green-900/30 text-green-400 border-green-500/30
                                                @elseif($domain->status === 'pending')
                                                    bg-yellow-900/30 text-yellow-400 border-yellow-500/30
                                                @elseif($domain->status === 'approved' || $domain->status === 'provisioning')
                                                    bg-purple-900/30 text-purple-400 border-purple-500/30
                                                @elseif($domain->status === 'suspended')
                                                    bg-orange-900/30 text-orange-400 border-orange-500/30
                                                @else
                                                    bg-red-900/30 text-red-400 border-red-500/30
                                                @endif
                                            ">
                                                {{ ucfirst($domain->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $domain->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            @if ($domain->status === 'pending')
                                                <form method="POST" action="{{ route('admin.domains.approve', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-green-400 hover:text-green-300 transition">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.domains.reject', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-red-400 hover:text-red-300 transition" onclick="return confirm('Are you sure?')">Reject</button>
                                                </form>
                                            @elseif ($domain->status === 'active')
                                                <form method="POST" action="{{ route('admin.domains.suspend', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-orange-400 hover:text-orange-300 transition" onclick="return confirm('Are you sure?')">Suspend</button>
                                                </form>
                                            @elseif ($domain->status === 'failed')
                                                <form method="POST" action="{{ route('admin.domains.retryProvision', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-blue-400 hover:text-blue-300 transition">Retry</button>
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
                        <p class="text-gray-400 text-lg">No domains found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
