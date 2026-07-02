@extends('layouts.app')

@section('page-title', 'Finance Reports')

@section('content')
    @php $money = fn ($value) => '$'.number_format((float) $value, 2); @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Finance Reports</h1>
            <p class="mt-1 text-sm text-slate-600">Account balances, income categories, expense categories, and date range transactions.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('finance-reports.general-ledger') }}" class="rounded-md bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-slate-800">General Ledger</a>
            <a href="{{ route('finance-reports.funeral') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Funeral Reconciliation</a>
        </div>
    </div>

    <form method="GET" class="mb-6 grid gap-3 rounded-md border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4">
        <div>
            <label for="assembly_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Assembly</label>
            <select id="assembly_id" name="assembly_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">All accessible assemblies</option>
                @foreach($assemblies as $assembly)
                    <option value="{{ $assembly->id }}" @selected((int) $selectedAssemblyId === $assembly->id)>{{ $assembly->name }}</option>
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
            <button class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Run Report</button>
        </div>
    </form>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-950">Account Balance Summary</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Account</th>
                        <th class="px-5 py-3 text-right">Income</th>
                        <th class="px-5 py-3 text-right">Expenses</th>
                        <th class="px-5 py-3 text-right">Balance</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($accountBalances as $account)
                        @php
                            $incomeTotal = (float) $account->income_total;
                            $expenseTotal = (float) $account->expense_total;
                            $balance = $incomeTotal - $expenseTotal;
                        @endphp
                        @continue($incomeTotal == 0.0 && $expenseTotal == 0.0)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-950">{{ $account->display_name }}</div>
                                <div class="text-xs capitalize text-slate-500">{{ $account->type }}</div>
                            </td>
                            <td class="px-5 py-4 text-right text-emerald-700">{{ $money($incomeTotal) }}</td>
                            <td class="px-5 py-4 text-right text-red-700">{{ $money($expenseTotal) }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-950">{{ $money($balance) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-950">Income by Category</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($incomeByCategory as $row)
                    <div class="flex items-center justify-between gap-4 px-5 py-4 text-sm">
                        <div>
                            <div class="font-medium text-slate-950">{{ $row->chartAccount?->display_name ?? ucfirst(str_replace('_', ' ', $row->type)) }}</div>
                            <div class="text-xs text-slate-500">Receipt type: {{ str_replace('_', ' ', $row->type) }}</div>
                        </div>
                        <div class="font-semibold text-emerald-700">{{ $money($row->total) }}</div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500">No income found for the selected date range.</div>
                @endforelse
            </div>
        </section>

        <section class="rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-950">Expenses by Category</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($expensesByCategory as $row)
                    <div class="flex items-center justify-between gap-4 px-5 py-4 text-sm">
                        <div>
                            <div class="font-medium text-slate-950">{{ $row->chartAccount?->display_name ?? ucfirst($row->category) }}</div>
                            <div class="text-xs text-slate-500">Expense category: {{ $row->category }}</div>
                        </div>
                        <div class="font-semibold text-red-700">{{ $money($row->total) }}</div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500">No expenses found for the selected date range.</div>
                @endforelse
            </div>
        </section>

        <section class="rounded-md border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-950">Date Range Transaction Report</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Account</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ optional($transaction['date'])->format('M d, Y') }}</td>
                            <td class="px-5 py-4 font-medium text-slate-950">{{ $transaction['kind'] }}</td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-700">{{ $transaction['account'] }}</div>
                                <div class="max-w-md truncate text-xs text-slate-500">{{ $transaction['description'] }}</div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold {{ $transaction['amount'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ $money($transaction['amount']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">No transactions found for the selected date range.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
