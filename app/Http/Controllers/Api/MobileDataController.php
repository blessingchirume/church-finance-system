<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assembly;
use App\Models\ChartAccount;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MobileDataController extends Controller
{
    public function assemblies(Request $request)
    {
        return response()->json([
            'data' => Assembly::whereIn('id', $request->user()->accessibleAssemblyIds())
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'location']),
        ]);
    }

    public function chartAccounts(Request $request)
    {
        return response()->json([
            'data' => ChartAccount::query()
                ->where('status', 'active')
                ->when($request->query('type'), fn ($query, $type) => $query->where('type', $type))
                ->whereIn('type', ['income', 'expense', 'asset', 'equity'])
                ->orderBy('code')
                ->get(['id', 'code', 'name', 'type']),
        ]);
    }

    public function storeTransaction(Request $request)
    {
        abort_unless($request->user()->canManageFinance(), 403);

        $validated = $request->validate([
            'mobile_client_id' => ['nullable', 'string', 'max:120'],
            'assembly_id' => ['required', 'exists:assemblies,id'],
            'date' => ['required', 'date'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'flow' => ['required', Rule::in(['offerings', 'pledges', 'funeral_contributions', 'general_income', 'expenses'])],
            'chart_account_id' => ['required', 'exists:chart_accounts,id'],
            'category_purpose' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'payment_method' => ['required', Rule::in(['cash', 'ecocash', 'bank_transfer', 'card', 'other'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        abort_unless($request->user()->canAccessAssembly((int) $validated['assembly_id']), 403);

        if (! empty($validated['mobile_client_id'])) {
            $existing = $validated['type'] === 'expense'
                ? Expense::with(['assembly', 'chartAccount'])->where('mobile_client_id', $validated['mobile_client_id'])->first()
                : Income::with(['assembly', 'chartAccount'])->where('mobile_client_id', $validated['mobile_client_id'])->first();

            if ($existing) {
                return response()->json([
                    'data' => $existing instanceof Expense
                        ? $this->expenseResource($existing)
                        : $this->incomeResource($existing),
                ]);
            }
        }

        $account = ChartAccount::findOrFail($validated['chart_account_id']);
        abort_unless($account->status === 'active', 422, 'Selected account is inactive.');

        if ($validated['type'] === 'expense') {
            abort_unless($account->type === 'expense', 422, 'Expenses must use an expense account.');

            $transaction = Expense::create([
                'assembly_id' => $validated['assembly_id'],
                'service_id' => null,
                'chart_account_id' => $account->id,
                'transaction_date' => $validated['date'],
                'amount' => $validated['amount'],
                'currency' => strtoupper($validated['currency']),
                'payment_method' => $validated['payment_method'],
                'mobile_client_id' => $validated['mobile_client_id'] ?? null,
                'submitted_from_mobile' => true,
                'description' => $validated['notes'] ?? null,
                'category' => $validated['category_purpose'],
                'purpose' => $validated['category_purpose'],
                'status' => 'approved',
                'created_by' => $request->user()->id,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            return response()->json(['data' => $this->expenseResource($transaction->load(['assembly', 'chartAccount']))], 201);
        }

        abort_unless($account->type === 'income', 422, 'Income must use an income account.');

        $transaction = Income::create([
            'assembly_id' => $validated['assembly_id'],
            'chart_account_id' => $account->id,
            'transaction_date' => $validated['date'],
            'type' => $this->incomeTypeForFlow($validated['flow']),
            'pledge_campaign' => $validated['flow'] === 'pledges' ? $validated['category_purpose'] : null,
            'purpose' => $validated['category_purpose'],
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency']),
            'payment_method' => $validated['payment_method'],
            'mobile_client_id' => $validated['mobile_client_id'] ?? null,
            'submitted_from_mobile' => true,
            'description' => $validated['notes'] ?? null,
            'source' => 'external',
            'status' => 'approved',
            'created_by' => $request->user()->id,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return response()->json(['data' => $this->incomeResource($transaction->load(['assembly', 'chartAccount']))], 201);
    }

    public function recentTransactions(Request $request)
    {
        $assemblyIds = $request->user()->accessibleAssemblyIds();

        $incomes = Income::with(['assembly', 'chartAccount'])
            ->where('created_by', $request->user()->id)
            ->whereIn('assembly_id', $assemblyIds)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Income $income) => $this->incomeResource($income));

        $expenses = Expense::with(['assembly', 'chartAccount'])
            ->where('created_by', $request->user()->id)
            ->whereIn('assembly_id', $assemblyIds)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Expense $expense) => $this->expenseResource($expense));

        return response()->json([
            'data' => $incomes
                ->merge($expenses)
                ->sortByDesc('created_at')
                ->values()
                ->take(20),
        ]);
    }

    private function incomeTypeForFlow(string $flow): string
    {
        return match ($flow) {
            'offerings' => 'offering',
            'pledges' => 'project_pledge',
            'funeral_contributions' => 'funeral',
            default => 'other',
        };
    }

    private function incomeResource(Income $income): array
    {
        return [
            'id' => $income->id,
            'record_type' => 'income',
            'assembly' => $income->assembly?->name,
            'date' => optional($income->transaction_date)->toDateString(),
            'flow' => $income->type,
            'account' => $income->chartAccount?->display_name,
            'category_purpose' => $income->purpose ?? $income->pledge_campaign,
            'amount' => (float) $income->amount,
            'currency' => $income->currency,
            'payment_method' => $income->payment_method,
            'status' => $income->status,
            'created_at' => $income->created_at?->toIso8601String(),
        ];
    }

    private function expenseResource(Expense $expense): array
    {
        return [
            'id' => $expense->id,
            'record_type' => 'expense',
            'assembly' => $expense->assembly?->name,
            'date' => optional($expense->transaction_date)->toDateString(),
            'flow' => 'expenses',
            'account' => $expense->chartAccount?->display_name,
            'category_purpose' => $expense->purpose ?? $expense->category,
            'amount' => (float) $expense->amount,
            'currency' => $expense->currency,
            'payment_method' => $expense->payment_method,
            'status' => $expense->status,
            'created_at' => $expense->created_at?->toIso8601String(),
        ];
    }
}
