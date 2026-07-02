@extends('layouts.app')

@section('page-title', 'Chart of Accounts')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Chart of Accounts</h1>
            <p class="mt-1 text-sm text-slate-600">Maintain account codes, categories, groupings, and active finance centers.</p>
        </div>
        @if(Auth::user()->canManageFinance())
            <a href="{{ route('chart-accounts.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">New Account</a>
        @endif
    </div>

    <form method="GET" class="mb-5 grid gap-3 rounded-md border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search code or name" class="rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
        <select name="type" class="rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
            <option value="">All account types</option>
            @foreach($types as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Apply Filters</button>
    </form>

    <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3">Account</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Parent</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($accounts as $account)
                    <tr class="hover:bg-slate-50">
                        <td class="whitespace-nowrap px-5 py-4 font-mono text-slate-700">{{ $account->code }}</td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-950">{{ $account->name }}</div>
                            <div class="max-w-lg truncate text-xs text-slate-500">{{ $account->description }}</div>
                        </td>
                        <td class="px-5 py-4 capitalize text-slate-600">{{ $account->type }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $account->parent?->display_name ?? 'Main account' }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize {{ $account->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $account->status }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if(Auth::user()->canManageFinance())
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('chart-accounts.edit', $account) }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Edit</a>
                                    @if(Auth::user()->hasRole('admin'))
                                        <form method="POST" action="{{ route('chart-accounts.destroy', $account) }}" onsubmit="return confirm('Delete this account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-semibold text-red-700 hover:text-red-900">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Read only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No accounts match the current filters.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $accounts->links() }}</div>
    </div>
@endsection
