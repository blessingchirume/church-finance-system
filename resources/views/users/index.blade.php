@extends('layouts.app')

@section('page-title', 'Users & Roles')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Users & Roles</h1>
        <p class="mt-1 text-sm text-slate-600">Control access for administrators, finance officers, and auditors.</p>
    </div>

    <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Current Role</th>
                    <th class="px-5 py-3 text-right">Update Role</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach($users as $user)
                    <tr>
                        <td class="px-5 py-4 font-medium text-slate-950">{{ $user->name }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium capitalize text-slate-700">{{ $user->role }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('users.role.update', $user) }}" class="flex justify-end gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                                <button class="rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
    </div>
@endsection
