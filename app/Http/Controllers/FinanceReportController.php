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

    public function funeral(Request $request)
    {
        $from = $request->date('from');
        $to = $request->date('to');

        $funeralIncome = Income::with(['chartAccount', 'member', 'creator', 'approver'])
            ->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'))
            ->where(function ($query) {
                $query->where('type', 'funeral')
                    ->orWhereHas('chartAccount', fn ($query) => $query
                        ->where('code', '4060')
                        ->orWhere('name', 'like', '%funeral%'));
            });

        $funeralExpenses = Expense::with(['chartAccount', 'service', 'creator', 'approver'])
            ->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'))
            ->where(function ($query) {
                $query->where('category', 'funeral')
                    ->orWhereHas('chartAccount', fn ($query) => $query
                        ->where('code', '5050')
                        ->orWhere('name', 'like', '%funeral%'));
            });

        $this->applyDateRange($funeralIncome, $from, $to);
        $this->applyDateRange($funeralExpenses, $from, $to);

        $collectionsTotal = (clone $funeralIncome)->sum('amount');
        $withdrawalsTotal = (clone $funeralExpenses)->sum('amount');

        $transactions = collect()
            ->merge((clone $funeralIncome)->latest()->get()->map(fn ($income) => [
                'date' => $income->created_at,
                'kind' => 'Collection',
                'reference' => $income->member?->name ?? $income->description ?? 'Funeral contribution',
                'account' => $income->chartAccount?->display_name ?? 'Funeral Contributions',
                'created_by' => $income->creator?->name,
                'approved_by' => $income->approver?->name,
                'amount' => $income->amount,
            ]))
            ->merge((clone $funeralExpenses)->latest()->get()->map(fn ($expense) => [
                'date' => $expense->created_at,
                'kind' => 'Withdrawal',
                'reference' => $expense->description ?? 'Funeral assistance payment',
                'account' => $expense->chartAccount?->display_name ?? 'Funeral Assistance Expenses',
                'created_by' => $expense->creator?->name,
                'approved_by' => $expense->approver?->name,
                'amount' => -1 * $expense->amount,
            ]))
            ->sortByDesc('date')
            ->values();

        return view('reports.funeral-reconciliation', [
            'from' => $from,
            'to' => $to,
            'collectionsTotal' => $collectionsTotal,
            'withdrawalsTotal' => $withdrawalsTotal,
            'remainingBalance' => $collectionsTotal - $withdrawalsTotal,
            'transactions' => $transactions,
        ]);
    }

    public function generalLedger(Request $request)
    {
        $from = $request->date('from');
        $to = $request->date('to');
        $accountId = $request->integer('chart_account_id') ?: null;

        $accounts = ChartAccount::orderBy('code')->get();

        $incomeQuery = Income::with(['chartAccount', 'member', 'creator', 'approver'])
            ->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'))
            ->when($accountId, fn ($query) => $query->where('chart_account_id', $accountId));

        $expenseQuery = Expense::with(['chartAccount', 'fundingAccount', 'service', 'creator', 'approver'])
            ->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status'))
            ->when($accountId, fn ($query) => $query->where(function ($query) use ($accountId) {
                $query->where('chart_account_id', $accountId)
                    ->orWhere('funding_account_id', $accountId);
            }));

        $this->applyDateRange($incomeQuery, $from, $to);
        $this->applyDateRange($expenseQuery, $from, $to);

        $incomeTotal = (clone $incomeQuery)->sum('amount');
        $expenseTotal = (clone $expenseQuery)->sum('amount');

        $accountSummaries = ChartAccount::query()
            ->withSum(['incomes as income_total' => fn ($query) => $this->applyDateRange($query->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status')), $from, $to)], 'amount')
            ->withSum(['expenses as expense_total' => fn ($query) => $this->applyDateRange($query->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status')), $from, $to)], 'amount')
            ->withSum(['fundedExpenses as funded_expense_total' => fn ($query) => $this->applyDateRange($query->where(fn ($query) => $query->where('status', 'approved')->orWhereNull('status')), $from, $to)], 'amount')
            ->orderBy('code')
            ->get()
            ->filter(fn ($account) => (float) $account->income_total !== 0.0 || (float) $account->expense_total !== 0.0 || (float) $account->funded_expense_total !== 0.0)
            ->values();

        $transactions = collect()
            ->merge((clone $incomeQuery)->latest()->get()->map(fn ($income) => [
                'date' => $income->created_at,
                'kind' => 'Income',
                'account' => $income->chartAccount?->display_name ?? 'Unassigned income account',
                'reference' => $income->member?->name ?? $income->pledge_campaign ?? $income->description ?? ucfirst(str_replace('_', ' ', $income->type)),
                'status' => $income->status ?? 'approved',
                'created_by' => $income->creator?->name,
                'approved_by' => $income->approver?->name,
                'debit' => 0,
                'credit' => $income->amount,
                'net' => $income->amount,
            ]))
            ->merge((clone $expenseQuery)->latest()->get()->map(function ($expense) use ($accountId) {
                $isFundDeduction = $accountId && (int) $expense->funding_account_id === (int) $accountId;

                return [
                'date' => $expense->created_at,
                'kind' => $isFundDeduction ? 'Payout' : 'Expense',
                'account' => $isFundDeduction
                    ? ($expense->fundingAccount?->display_name ?? 'Funding account')
                    : ($expense->chartAccount?->display_name ?? 'Unassigned expense account'),
                'reference' => $expense->description ?? ucfirst($expense->category),
                'status' => $expense->status ?? 'approved',
                'created_by' => $expense->creator?->name,
                'approved_by' => $expense->approver?->name,
                'debit' => $expense->amount,
                'credit' => 0,
                'net' => -1 * $expense->amount,
                ];
            }))
            ->sortByDesc('date')
            ->values();

        return view('reports.general-ledger', [
            'accounts' => $accounts,
            'selectedAccount' => $accountId ? $accounts->firstWhere('id', $accountId) : null,
            'accountSummaries' => $accountSummaries,
            'transactions' => $transactions,
            'from' => $from,
            'to' => $to,
            'accountId' => $accountId,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'netMovement' => $incomeTotal - $expenseTotal,
        ]);
    }

    private function applyDateRange($query, $from, $to)
    {
        return $query
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to));
    }
}
