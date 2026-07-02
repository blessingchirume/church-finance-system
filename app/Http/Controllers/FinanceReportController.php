<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from');
        $to = $request->date('to');

        $incomeQuery = Income::with('chartAccount')->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'));
        $expenseQuery = Expense::with('chartAccount')->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'));

        if ($from) {
            $incomeQuery->whereDate('created_at', '>=', $from);
            $expenseQuery->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $incomeQuery->whereDate('created_at', '<=', $to);
            $expenseQuery->whereDate('created_at', '<=', $to);
        }

        $incomeByCategory = (clone $incomeQuery)
            ->selectRaw('chart_account_id, type, sum(amount) as total')
            ->groupBy('chart_account_id', 'type')
            ->orderByDesc('total')
            ->get();

        $expensesByCategory = (clone $expenseQuery)
            ->selectRaw('chart_account_id, category, sum(amount) as total')
            ->groupBy('chart_account_id', 'category')
            ->orderByDesc('total')
            ->get();

        $accountBalances = ChartAccount::query()
            ->withSum(['incomes as income_total' => fn ($query) => $this->applyDateRange($query->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status')), $from, $to)], 'amount')
            ->withSum(['expenses as expense_total' => fn ($query) => $this->applyDateRange($query->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status')), $from, $to)], 'amount')
            ->orderBy('code')
            ->get();

        $transactions = collect()
            ->merge((clone $incomeQuery)->latest()->take(50)->get()->map(fn ($income) => [
                'date' => $income->created_at,
                'kind' => 'Income',
                'account' => $income->chartAccount?->display_name ?? ucfirst(str_replace('_', ' ', $income->type)),
                'description' => $income->description,
                'amount' => $income->amount,
            ]))
            ->merge((clone $expenseQuery)->latest()->take(50)->get()->map(fn ($expense) => [
                'date' => $expense->created_at,
                'kind' => 'Expense',
                'account' => $expense->chartAccount?->display_name ?? ucfirst($expense->category),
                'description' => $expense->description,
                'amount' => -1 * $expense->amount,
            ]))
            ->sortByDesc('date')
            ->take(50);

        return view('reports.finance', compact('accountBalances', 'incomeByCategory', 'expensesByCategory', 'transactions', 'from', 'to'));
    }

    private function applyDateRange($query, $from, $to)
    {
        return $query
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to));
    }
}
