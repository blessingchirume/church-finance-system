@extends('layouts.app')

@section('page-title', 'General Ledger')

@section('content')
    @php $money = fn ($value) => '$'.number_format((float) $value, 2); @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">General Ledger</h1>
            <p class="mt-1 text-sm text-slate-600">Reconcile collections, payments, and net movement for every G/L account.</p>
        </div>
        <a href="{{ route('finance-reports.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">All Reports</a>
    </div>

    <form method="GET" class="mb-6 grid gap-3 rounded-md border border-slate-200 bg-white p-4 shadow-sm lg:grid-cols-4">
        <div>
            <label for="chart_account_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">G/L Account</label>
            <select id="chart_account_id" name="chart_account_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">All accounts</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" @selected((int) $accountId === $account->id)>{{ $account->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="from" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">From</label>
            <input id="from" type="date" name="from" value="{{ optional($from)->format('Y-m-d') }}" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
        </div>
        <div>
            <label for="to" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">To</label>
            <input id="to" type="date" name="to" value="{{ optional($to)->format('Y-m-d') }}" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
        </div>
        <div class="flex items-end">
            <button class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Run Ledger</button>
        </div>
    </form>

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Income / Credits</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $money($incomeTotal) }}</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Expenses / Debits</p>
            <p class="mt-2 text-2xl font-semibold text-red-700">{{ $money($expenseTotal) }}</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">{{ $selectedAccount ? 'Selected Account Movement' : 'Net Movement' }}</p>
            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $money($netMovement) }}</p>
        </div>
    </div>

    @unless($selectedAccount)
        <section class="mb-6 rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-950">G/L Account Summary</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Account</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3 text-right">Income / Credits</th>
                        <th class="px-5 py-3 text-right">Expenses / Debits</th>
                        <th class="px-5 py-3 text-right">Net</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($accountSummaries as $account)
                        @php
                            $accountIncome = (float) $account->income_total;
                            $accountExpense = (float) $account->expense_total;
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-medium text-slate-950">{{ $account->display_name }}</td>
                            <td class="px-5 py-4 capitalize text-slate-600">{{ $account->type }}</td>
                            <td class="px-5 py-4 text-right text-emerald-700">{{ $money($accountIncome) }}</td>
                            <td class="px-5 py-4 text-right text-red-700">{{ $money($accountExpense) }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-950">{{ $money($accountIncome - $accountExpense) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No G/L activity found for this date range.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endunless

    <section class="rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-950">{{ $selectedAccount?->display_name ?? 'All G/L Transactions' }}</h2>
            <p class="mt-1 text-sm text-slate-500">Income records increase movement. Expense records reduce movement.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Account</th>
                    <th class="px-5 py-3">Reference</th>
                    <th class="px-5 py-3">Prepared / Approved</th>
                    <th class="px-5 py-3 text-right">Debit</th>
                    <th class="px-5 py-3 text-right">Credit</th>
                    <th class="px-5 py-3 text-right">Net</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $transaction)
                    <tr>
                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ optional($transaction['date'])->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction['kind'] === 'Income' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $transaction['kind'] }}</span>
                        </td>
                        <td class="px-5 py-4 font-medium text-slate-800">{{ $transaction['account'] }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $transaction['reference'] }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            <div>{{ $transaction['created_by'] ?? 'Not captured' }}</div>
                            <div class="text-xs text-slate-500">Approved: {{ $transaction['approved_by'] ?? 'Not captured' }}</div>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right text-red-700">{{ $transaction['debit'] > 0 ? $money($transaction['debit']) : '-' }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right text-emerald-700">{{ $transaction['credit'] > 0 ? $money($transaction['credit']) : '-' }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold {{ $transaction['net'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ $money($transaction['net']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-sm text-slate-500">No ledger transactions found for this filter.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
