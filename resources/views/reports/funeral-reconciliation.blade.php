@extends('layouts.app')

@section('page-title', 'Funeral Reconciliation')

@section('content')
    @php $money = fn ($value) => '$'.number_format((float) $value, 2); @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Funeral Fund Reconciliation</h1>
            <p class="mt-1 text-sm text-slate-600">Compares funeral contributions collected against funeral assistance payments and withdrawals.</p>
        </div>
        <a href="{{ route('finance-reports.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">All Reports</a>
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
            <button class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Run Reconciliation</button>
        </div>
    </form>

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Funeral Contributions Collected</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $money($collectionsTotal) }}</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Funeral Payments / Withdrawals</p>
            <p class="mt-2 text-2xl font-semibold text-red-700">{{ $money($withdrawalsTotal) }}</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Remaining Funeral Fund Balance</p>
            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $money($remainingBalance) }}</p>
        </div>
    </div>

    <section class="rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-950">Funeral Reconciliation Ledger</h2>
            <p class="mt-1 text-sm text-slate-500">Collections increase the balance. Withdrawals and payments reduce the balance.</p>
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
                    <th class="px-5 py-3 text-right">Amount</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $transaction)
                    <tr>
                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ optional($transaction['date'])->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction['kind'] === 'Collection' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">{{ $transaction['kind'] }}</span>
                        </td>
                        <td class="px-5 py-4 font-medium text-slate-800">{{ $transaction['account'] }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $transaction['reference'] }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            <div>{{ $transaction['created_by'] ?? 'Not captured' }}</div>
                            <div class="text-xs text-slate-500">Approved: {{ $transaction['approved_by'] ?? 'Not captured' }}</div>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold {{ $transaction['amount'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ $money($transaction['amount']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No funeral collections or payments were found for this date range.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
