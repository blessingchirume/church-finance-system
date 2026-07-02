@extends('layouts.app')

@section('page-title', 'Executive Dashboard')

@section('content')
    @php
        $money = fn ($value) => '$'.number_format((float) $value, 2);
        $cards = [
            ['label' => 'Total Income', 'value' => $money($totalIncome), 'tone' => 'emerald', 'note' => 'Approved receipts'],
            ['label' => 'Total Expenses', 'value' => $money($totalExpenses), 'tone' => 'red', 'note' => 'Approved payments'],
            ['label' => 'Net Balance', 'value' => $money($netBalance), 'tone' => 'slate', 'note' => 'Income less expenses'],
            ['label' => 'Pledges Collected', 'value' => $money($pledgesCollected), 'tone' => 'amber', 'note' => 'Project pledge receipts'],
            ['label' => 'Funeral Contributions', 'value' => $money($funeralContributions), 'tone' => 'indigo', 'note' => 'Member and receipt records'],
            ['label' => 'General Revenue', 'value' => $money($generalRevenue), 'tone' => 'cyan', 'note' => 'Offering and general income'],
        ];
        $toneClasses = [
            'emerald' => 'bg-emerald-50 text-emerald-700',
            'red' => 'bg-red-50 text-red-700',
            'slate' => 'bg-slate-100 text-slate-700',
            'amber' => 'bg-amber-50 text-amber-700',
            'indigo' => 'bg-indigo-50 text-indigo-700',
            'cyan' => 'bg-cyan-50 text-cyan-700',
        ];
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Finance Overview</h1>
            <p class="mt-1 text-sm text-slate-600">Live view of receipts, payments, pledges, and account-level performance.</p>
        </div>
        @if(Auth::user()->canManageFinance())
            <div class="grid grid-cols-2 gap-3 sm:flex">
                <a href="{{ route('incomes.create') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Record Income</a>
                <a href="{{ route('expenses.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Record Expense</a>
            </div>
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($cards as $card)
            <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $card['value'] }}</p>
                    </div>
                    <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $toneClasses[$card['tone']] }}">{{ $card['note'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="rounded-md border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-950">Recent Transactions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Account</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ optional($transaction['date'])->format('M d, Y') }}</td>
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $transaction['type'] }}</td>
                            <td class="px-5 py-4 text-slate-600">
                                <div class="font-medium text-slate-800">{{ $transaction['account'] }}</div>
                                <div class="max-w-md truncate text-xs text-slate-500">{{ $transaction['assembly'] ?? 'Unassigned assembly' }} - {{ $transaction['description'] ?: 'No description captured' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium capitalize text-slate-700">{{ str_replace('_', ' ', $transaction['status']) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold {{ $transaction['amount'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ $money($transaction['amount']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">No transactions have been recorded yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-950">Income Breakdown</h2>
                <span class="text-xs font-medium text-slate-500">{{ $incomeBreakdown->count() }} accounts</span>
            </div>
            <div class="mt-5 space-y-4">
                @forelse($incomeBreakdown as $account)
                    @php $percent = $totalIncome > 0 ? min(100, ((float) $account->received_total / $totalIncome) * 100) : 0; @endphp
                    <div>
                        <div class="mb-1 flex justify-between gap-3 text-sm">
                            <span class="font-medium text-slate-700">{{ $account->name }}</span>
                            <span class="font-semibold text-slate-950">{{ $money($account->received_total) }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-md border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">Income accounts will appear here after receipts are posted.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Pending Approvals</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $pendingApprovals }}</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Members</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($memberCount) }}</p>
        </div>
        <div class="rounded-md border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Monthly Trend Points</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $monthlyTrend->count() }}</p>
        </div>
    </div>

    <section class="mt-6 rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-950">Totals Per Assembly</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Assembly</th>
                    <th class="px-5 py-3 text-right">Income</th>
                    <th class="px-5 py-3 text-right">Expenses</th>
                    <th class="px-5 py-3 text-right">Net</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($assemblyTotals as $assembly)
                    @php
                        $assemblyIncome = (float) $assembly->income_total;
                        $assemblyExpenses = (float) $assembly->expense_total;
                    @endphp
                    <tr>
                        <td class="px-5 py-4 font-medium text-slate-950">{{ $assembly->name }}</td>
                        <td class="px-5 py-4 text-right font-semibold text-emerald-700">{{ $money($assemblyIncome) }}</td>
                        <td class="px-5 py-4 text-right font-semibold text-red-700">{{ $money($assemblyExpenses) }}</td>
                        <td class="px-5 py-4 text-right font-semibold text-slate-950">{{ $money($assemblyIncome - $assemblyExpenses) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">No assembly totals available.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
