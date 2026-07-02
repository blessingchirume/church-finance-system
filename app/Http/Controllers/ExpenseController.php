<?php

namespace App\Http\Controllers;

use App\Models\Assembly;
use App\Models\ChartAccount;
use App\Models\Expense;
use App\Models\Service;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $assemblyIds = request()->user()->accessibleAssemblyIds();
        $selectedAssemblyId = request('assembly_id');

        $expenses = Expense::with(['assembly', 'service', 'chartAccount', 'fundingAccount', 'creator', 'approver'])
            ->whereIn('assembly_id', $assemblyIds)
            ->when($selectedAssemblyId, fn ($query) => $query->where('assembly_id', $selectedAssemblyId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('expenses.index', [
            'expenses' => $expenses,
            'assemblies' => Assembly::whereIn('id', $assemblyIds)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        abort_unless(request()->user()->canManageFinance(), 403);

        $services = Service::orderBy('service_date', 'desc')->get();
        $accounts = ChartAccount::where('type', 'expense')->where('status', 'active')->orderBy('code')->get();
        $fundingAccounts = ChartAccount::whereIn('type', ['asset', 'income', 'equity'])->where('status', 'active')->orderBy('code')->get();
        $categories = ['worship', 'maintenance', 'outreach', 'administration', 'funeral', 'other'];
        $statuses = ['draft', 'pending_approval', 'approved', 'rejected', 'reversed'];
        $assemblies = Assembly::whereIn('id', request()->user()->accessibleAssemblyIds())->where('status', 'active')->orderBy('name')->get();

        return view('expenses.form', compact('services', 'accounts', 'fundingAccounts', 'categories', 'statuses', 'assemblies'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canManageFinance(), 403);

        $validated = $request->validate([
            'assembly_id' => 'required|exists:assemblies,id',
            'service_id' => 'required|exists:services,id',
            'chart_account_id' => 'required|exists:chart_accounts,id',
            'funding_account_id' => 'nullable|exists:chart_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:worship,maintenance,outreach,administration,funeral,other',
            'status' => 'required|in:draft,pending_approval,approved,rejected,reversed',
        ]);
        abort_unless($request->user()->canAccessAssembly((int) $validated['assembly_id']), 403);

        $validated['created_by'] = $request->user()->id;
        if ($validated['status'] === 'approved') {
            $validated['approved_by'] = $request->user()->id;
            $validated['approved_at'] = now();
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully');
    }

    public function edit(Expense $expense)
    {
        abort_unless(request()->user()->canManageFinance(), 403);

        $services = Service::orderBy('service_date', 'desc')->get();
        $accounts = ChartAccount::where('type', 'expense')->where('status', 'active')->orderBy('code')->get();
        $fundingAccounts = ChartAccount::whereIn('type', ['asset', 'income', 'equity'])->where('status', 'active')->orderBy('code')->get();
        $categories = ['worship', 'maintenance', 'outreach', 'administration', 'funeral', 'other'];
        $statuses = ['draft', 'pending_approval', 'approved', 'rejected', 'reversed'];
        $assemblies = Assembly::whereIn('id', request()->user()->accessibleAssemblyIds())->where('status', 'active')->orderBy('name')->get();

        abort_unless(request()->user()->canAccessAssembly((int) $expense->assembly_id), 403);

        return view('expenses.form', compact('expense', 'services', 'accounts', 'fundingAccounts', 'categories', 'statuses', 'assemblies'));
    }

    public function update(Request $request, Expense $expense)
    {
        abort_unless($request->user()->canManageFinance(), 403);

        $validated = $request->validate([
            'assembly_id' => 'required|exists:assemblies,id',
            'service_id' => 'required|exists:services,id',
            'chart_account_id' => 'required|exists:chart_accounts,id',
            'funding_account_id' => 'nullable|exists:chart_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:worship,maintenance,outreach,administration,funeral,other',
            'status' => 'required|in:draft,pending_approval,approved,rejected,reversed',
        ]);
        abort_unless($request->user()->canAccessAssembly((int) $validated['assembly_id']), 403);
        abort_unless($request->user()->canAccessAssembly((int) $expense->assembly_id), 403);

        $validated['updated_by'] = $request->user()->id;
        if ($validated['status'] === 'approved' && $expense->status !== 'approved') {
            $validated['approved_by'] = $request->user()->id;
            $validated['approved_at'] = now();
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        abort_unless(request()->user()->hasRole('admin'), 403);

        $expense->delete();
        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
