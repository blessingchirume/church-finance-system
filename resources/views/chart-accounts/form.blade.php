@extends('layouts.app')

@section('page-title', isset($account->id) ? 'Edit Account' : 'New Account')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">{{ isset($account->id) ? 'Edit Account' : 'New Account' }}</h1>
            <p class="mt-1 text-sm text-slate-600">Use stable account codes so reports remain consistent over time.</p>
        </div>

        <form method="POST" action="{{ isset($account->id) ? route('chart-accounts.update', $account) : route('chart-accounts.store') }}" class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @isset($account->id)
                @method('PUT')
            @endisset

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="code" class="block text-sm font-semibold text-slate-700">Account Code</label>
                    <input id="code" name="code" value="{{ old('code', $account->code) }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">Account Name</label>
                    <input id="name" name="name" value="{{ old('name', $account->name) }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700">Account Type</label>
                    <select id="type" name="type" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        @foreach($types as $type)
                            <option value="{{ $type }}" @selected(old('type', $account->type) === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">Status</label>
                    <select id="status" name="status" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="active" @selected(old('status', $account->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $account->status) === 'inactive')>Inactive</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="parent_id" class="block text-sm font-semibold text-slate-700">Parent Account</label>
                    <select id="parent_id" name="parent_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Main account</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" @selected((int) old('parent_id', $account->parent_id) === $parent->id)>{{ $parent->display_name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700">Description</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('description', $account->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('chart-accounts.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save Account</button>
            </div>
        </form>
    </div>
@endsection
