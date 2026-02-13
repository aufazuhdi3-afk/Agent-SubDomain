@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="text-2xl font-bold mb-6">Domain Management</h2>

                @if ($message = session('success'))
                    <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 border border-green-400 rounded">
                        {{ $message }}
                    </div>
                @endif

                @if ($message = session('error'))
                    <div class="mb-4 px-4 py-2 bg-red-100 text-red-700 border border-red-400 rounded">
                        {{ $message }}
                    </div>
                @endif

                <!-- Filter by Status -->
                <div class="mb-6 flex gap-2">
                    <a
                        href="{{ route('admin.domains.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition duration-150 ease-in-out 
                        {{ is_null($status) ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        All
                    </a>
                    @foreach(['pending', 'approved', 'provisioning', 'active', 'failed', 'suspended'] as $s)
                        <a
                            href="{{ route('admin.domains.index', ['status' => $s]) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition duration-150 ease-in-out 
                            {{ $status === $s ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            {{ ucfirst($s) }}
                        </a>
                    @endforeach
                </div>

                @if ($domains->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Domain</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target IP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($domains as $domain)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $domain->user->name }} <br>
                                            <span class="text-xs text-gray-500">{{ $domain->user->email }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $domain->full_domain }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $domain->target_ip ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($domain->status === 'active')
                                                    bg-green-100 text-green-800
                                                @elseif($domain->status === 'pending')
                                                    bg-yellow-100 text-yellow-800
                                                @elseif($domain->status === 'approved' || $domain->status === 'provisioning')
                                                    bg-blue-100 text-blue-800
                                                @else
                                                    bg-red-100 text-red-800
                                                @endif
                                            ">
                                                {{ ucfirst($domain->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $domain->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-y-1">
                                            @if ($domain->status === 'pending')
                                                <form method="POST" action="{{ route('admin.domains.approve', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 dark:hover:text-green-400 block">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.domains.reject', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:hover:text-red-400 block" onclick="return confirm('Are you sure?')">Reject</button>
                                                </form>
                                            @elseif ($domain->status === 'active')
                                                <form method="POST" action="{{ route('admin.domains.suspend', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-orange-600 hover:text-orange-900 dark:hover:text-orange-400 block" onclick="return confirm('Are you sure?')">Suspend</button>
                                                </form>
                                            @elseif ($domain->status === 'failed')
                                                <form method="POST" action="{{ route('admin.domains.retryProvision', $domain) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 block">Retry</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">-</span>
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
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">No domains found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
