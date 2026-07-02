@extends('layouts.app')

@section('page-title', isset($assembly->id) ? 'Edit Assembly' : 'New Assembly')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">{{ isset($assembly->id) ? 'Edit Assembly' : 'New Assembly' }}</h1>
            <p class="mt-1 text-sm text-slate-600">Create branch records used for transaction filtering and user access.</p>
        </div>

        <form method="POST" action="{{ isset($assembly->id) ? route('assemblies.update', $assembly) : route('assemblies.store') }}" class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @isset($assembly->id) @method('PUT') @endisset

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-slate-700" for="name">Assembly Name</label>
                    <input id="name" name="name" value="{{ old('name', $assembly->name) }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700" for="code">Code</label>
                    <input id="code" name="code" value="{{ old('code', $assembly->code) }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm uppercase focus:border-emerald-600 focus:ring-emerald-600">
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700" for="location">Location</label>
                    <input id="location" name="location" value="{{ old('location', $assembly->location) }}" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700" for="status">Status</label>
                    <select id="status" name="status" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="active" @selected(old('status', $assembly->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $assembly->status) === 'inactive')>Inactive</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('assemblies.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save Assembly</button>
            </div>
        </form>
    </div>
@endsection
