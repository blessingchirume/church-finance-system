<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\Expense;
use App\Models\FuneralContribution;
use App\Models\Income;
use App\Models\Member;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $approvedIncome = Income::query()->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'));
        $approvedExpenses = Expense::query()->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'));

        $totalIncome = (clone $approvedIncome)->sum('amount');
        $totalExpenses = (clone $approvedExpenses)->sum('amount');

        $incomeBreakdown = ChartAccount::query()
            ->where('type', 'income')
            ->withSum(['incomes as received_total' => fn ($query) => $query->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'))], 'amount')
            ->orderBy('code')
            ->get()
            ->filter(fn ($account) => (float) $account->received_total > 0)
            ->take(8);

        $monthlyTrend = Income::query()
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'))
            ->get()
            ->groupBy(fn ($income) => $income->created_at->format('Y-m'))
            ->map(fn ($rows, $period) => (object) ['period' => $period, 'income_total' => $rows->sum('amount')])
            ->values();

        $recentTransactions = collect()
            ->merge(Income::with(['member', 'chartAccount'])->latest()->take(6)->get()->map(fn ($income) => [
                'date' => $income->created_at,
                'type' => 'Income',
                'account' => $income->chartAccount?->name ?? ucfirst(str_replace('_', ' ', $income->type)),
                'description' => $income->description ?: $income->member?->name,
                'amount' => $income->amount,
                'status' => $income->status ?? 'approved',
            ]))
            ->merge(Expense::with(['chartAccount'])->latest()->take(6)->get()->map(fn ($expense) => [
                'date' => $expense->created_at,
                'type' => 'Expense',
                'account' => $expense->chartAccount?->name ?? ucfirst($expense->category),
                'description' => $expense->description,
                'amount' => -1 * $expense->amount,
                'status' => $expense->status ?? 'approved',
            ]))
            ->sortByDesc('date')
            ->take(8);

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netBalance' => $totalIncome - $totalExpenses,
            'pledgesCollected' => (clone $approvedIncome)->where('type', 'project_pledge')->sum('amount'),
            'funeralContributions' => FuneralContribution::sum('amount') + (clone $approvedIncome)->where('type', 'funeral')->sum('amount'),
            'generalRevenue' => (clone $approvedIncome)->where('type', 'offering')->sum('amount'),
            'memberCount' => Member::count(),
            'incomeBreakdown' => $incomeBreakdown,
            'monthlyTrend' => $monthlyTrend,
            'recentTransactions' => $recentTransactions,
            'pendingApprovals' => Income::where('status', 'pending_approval')->count() + Expense::where('status', 'pending_approval')->count(),
        ]);
    }
}
