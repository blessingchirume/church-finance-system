<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\Expense;
use App\Models\Service;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['service', 'chartAccount', 'creator', 'approver'])
            ->latest()
            ->paginate(15);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        abort_unless(request()->user()->canManageFinance(), 403);

        $services = Service::orderBy('service_date', 'desc')->get();
        $accounts = ChartAccount::where('type', 'expense')->where('status', 'active')->orderBy('code')->get();
        $categories = ['worship', 'maintenance', 'outreach', 'administration', 'other'];
        $statuses = ['draft', 'pending_approval', 'approved', 'rejected', 'reversed'];

        return view('expenses.form', compact('services', 'accounts', 'categories', 'statuses'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canManageFinance(), 403);

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'chart_account_id' => 'required|exists:chart_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:worship,maintenance,outreach,administration,other',
            'status' => 'required|in:draft,pending_approval,approved,rejected,reversed',
        ]);

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
        $categories = ['worship', 'maintenance', 'outreach', 'administration', 'other'];
        $statuses = ['draft', 'pending_approval', 'approved', 'rejected', 'reversed'];

        return view('expenses.form', compact('expense', 'services', 'accounts', 'categories', 'statuses'));
    }

    public function update(Request $request, Expense $expense)
    {
        abort_unless($request->user()->canManageFinance(), 403);

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'chart_account_id' => 'required|exists:chart_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:worship,maintenance,outreach,administration,other',
            'status' => 'required|in:draft,pending_approval,approved,rejected,reversed',
        ]);

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
