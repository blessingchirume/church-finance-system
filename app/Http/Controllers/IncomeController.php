<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\Income;
use App\Models\Member;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['member', 'service', 'project', 'chartAccount', 'creator', 'approver'])
            ->latest()
            ->paginate(15);

        return view('incomes.index', compact('incomes'));
    }

    public function create()
    {
        abort_unless(request()->user()->canManageFinance(), 403);

        $members = Member::whereStatus('active')->orderBy('name')->get();
        $services = Service::orderBy('service_date', 'desc')->get();
        $projects = Project::whereStatus('active')->orderBy('name')->get();
        $accounts = ChartAccount::where('type', 'income')->where('status', 'active')->orderBy('code')->get();
        $types = ['partnership', 'offering', 'project_pledge', 'funeral', 'tuckshop', 'other'];
        $sources = ['manual', 'external'];
        $statuses = ['draft', 'pending_approval', 'approved', 'rejected', 'reversed'];

        return view('incomes.form', compact('members', 'services', 'projects', 'accounts', 'types', 'sources', 'statuses'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canManageFinance(), 403);

        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'service_id' => 'nullable|exists:services,id',
            'project_id' => 'nullable|exists:projects,id',
            'chart_account_id' => 'required|exists:chart_accounts,id',
            'type' => 'required|in:partnership,offering,project_pledge,funeral,tuckshop,other',
            'pledge_campaign' => 'nullable|string|max:150',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'source' => 'required|in:manual,external',
            'status' => 'required|in:draft,pending_approval,approved,rejected,reversed',
        ]);

        $validated['created_by'] = $request->user()->id;
        if ($validated['status'] === 'approved') {
            $validated['approved_by'] = $request->user()->id;
            $validated['approved_at'] = now();
        }

        Income::create($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income recorded successfully');
    }

    public function edit(Income $income)
    {
        abort_unless(request()->user()->canManageFinance(), 403);

        $members = Member::whereStatus('active')->orderBy('name')->get();
        $services = Service::orderBy('service_date', 'desc')->get();
        $projects =Project::whereStatus('active')->orderBy('name')->get();
        $accounts = ChartAccount::where('type', 'income')->where('status', 'active')->orderBy('code')->get();
        $types = ['partnership', 'offering', 'project_pledge', 'funeral', 'tuckshop', 'other'];
        $sources = ['manual', 'external'];
        $statuses = ['draft', 'pending_approval', 'approved', 'rejected', 'reversed'];

        return view('incomes.form', compact('income', 'members', 'services', 'projects', 'accounts', 'types', 'sources', 'statuses'));
    }

    public function update(Request $request, Income $income)
    {
        abort_unless($request->user()->canManageFinance(), 403);

        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'service_id' => 'nullable|exists:services,id',
            'project_id' => 'nullable|exists:projects,id',
            'chart_account_id' => 'required|exists:chart_accounts,id',
            'type' => 'required|in:partnership,offering,project_pledge,funeral,tuckshop,other',
            'pledge_campaign' => 'nullable|string|max:150',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'source' => 'required|in:manual,external',
            'status' => 'required|in:draft,pending_approval,approved,rejected,reversed',
        ]);

        $validated['updated_by'] = $request->user()->id;
        if ($validated['status'] === 'approved' && $income->status !== 'approved') {
            $validated['approved_by'] = $request->user()->id;
            $validated['approved_at'] = now();
        }

        $income->update($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income updated successfully');
    }

    public function destroy(Income $income)
    {
        abort_unless(request()->user()->hasRole('admin'), 403);

        $income->delete();
        return redirect()->route('incomes.index')
            ->with('success', 'Income deleted successfully');
    }
}
