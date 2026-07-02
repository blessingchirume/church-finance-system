@extends('layouts.app')

@section('page-title', isset($expense) ? 'Edit Payment' : 'Record Payment')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">{{ isset($expense) ? 'Edit Expense' : 'Record Expense' }}</h1>
            <p class="mt-1 text-sm text-slate-600">Allocate every payment to the correct expense account for reporting.</p>
        </div>

        <form method="POST" action="{{ isset($expense) ? route('expenses.update', $expense) : route('expenses.store') }}" class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @isset($expense)
                @method('PUT')
            @endisset

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="chart_account_id" class="block text-sm font-semibold text-slate-700">G/L Expense Account</label>
                    <select id="chart_account_id" name="chart_account_id" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Select expense account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" @selected((int) old('chart_account_id', $expense->chart_account_id ?? null) === $account->id)>{{ $account->display_name }}</option>
                        @endforeach
                    </select>
                    @error('chart_account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="funding_account_id" class="block text-sm font-semibold text-slate-700">Paid From / Fund Source</label>
                    <select id="funding_account_id" name="funding_account_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">General cash / not assigned</option>
                        @foreach($fundingAccounts as $account)
                            <option value="{{ $account->id }}" @selected((int) old('funding_account_id', $expense->funding_account_id ?? null) === $account->id)>{{ $account->display_name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Use this when the payment should reduce a specific income or fund balance.</p>
                    @error('funding_account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="category" class="block text-sm font-semibold text-slate-700">Category</label>
                    <select id="category" name="category" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(old('category', $expense->category ?? null) === $category)>{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="service_id" class="block text-sm font-semibold text-slate-700">Service</label>
                    <select id="service_id" name="service_id" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Select service</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @selected((int) old('service_id', $expense->service_id ?? null) === $service->id)>{{ $service->service_date }}</option>
                        @endforeach
                    </select>
                    @error('service_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="amount" class="block text-sm font-semibold text-slate-700">Amount</label>
                    <input type="number" step="0.01" id="amount" name="amount" min="0" value="{{ old('amount', $expense->amount ?? null) }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">Status</label>
                    <select id="status" name="status" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $expense->status ?? 'approved') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700">Description / Memo</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('description', $expense->description ?? null) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('expenses.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save Expense</button>
            </div>
        </form>
    </div>
@endsection
