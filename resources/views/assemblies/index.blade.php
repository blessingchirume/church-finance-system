@extends('layouts.app')

@section('page-title', 'Assemblies')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Assemblies / Branches</h1>
            <p class="mt-1 text-sm text-slate-600">Manage church assemblies and branch-level access.</p>
        </div>
        <a href="{{ route('assemblies.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">New Assembly</a>
    </div>

    <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-5 py-3">Assembly</th>
                <th class="px-5 py-3">Code</th>
                <th class="px-5 py-3">Location</th>
                <th class="px-5 py-3">Users</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($assemblies as $assembly)
                <tr>
                    <td class="px-5 py-4 font-medium text-slate-950">{{ $assembly->name }}</td>
                    <td class="px-5 py-4 font-mono text-slate-600">{{ $assembly->code }}</td>
                    <td class="px-5 py-4 text-slate-600">{{ $assembly->location ?? 'N/A' }}</td>
                    <td class="px-5 py-4 text-slate-600">{{ $assembly->users_count }}</td>
                    <td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium capitalize text-slate-700">{{ $assembly->status }}</span></td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('assemblies.edit', $assembly) }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Edit</a>
                            <form method="POST" action="{{ route('assemblies.destroy', $assembly) }}" onsubmit="return confirm('Delete this assembly?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm font-semibold text-red-700 hover:text-red-900">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No assemblies found.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="border-t border-slate-200 px-5 py-4">{{ $assemblies->links() }}</div>
    </div>
@endsection
