@extends('layouts.app')

@section('page-title', 'Expenses / Payments')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">Expenses / Payments</h1>
            <p class="mt-1 text-sm text-slate-600">Capture operational payments and allocate them to cost centers.</p>
        </div>
        @if(Auth::user()->canManageFinance())
            <a href="{{ route('expenses.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Record Expense</a>
        @endif
    </div>

    <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Account</th>
                    <th class="px-5 py-3">Paid From</th>
                    <th class="px-5 py-3">Service</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50">
                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $expense->created_at?->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-950">{{ $expense->chartAccount?->display_name ?? 'Unassigned account' }}</div>
                            <div class="text-xs capitalize text-slate-500">{{ $expense->category }}</div>
                            <div class="max-w-md truncate text-xs text-slate-500">{{ $expense->description }}</div>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $expense->fundingAccount?->display_name ?? 'General cash / not assigned' }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $expense->service?->service_date ?? 'N/A' }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium capitalize text-slate-700">{{ str_replace('_', ' ', $expense->status ?? 'approved') }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-red-700">${{ number_format($expense->amount, 2) }}</td>
                        <td class="px-5 py-4 text-right">
                            @if(Auth::user()->canManageFinance())
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Edit</a>
                                    @if(Auth::user()->hasRole('admin'))
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense record?')">
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
                        <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-500">No expense records have been captured yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $expenses->links() }}</div>
    </div>
@endsection
