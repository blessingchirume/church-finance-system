<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Member;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['member', 'service', 'project'])
            ->latest()
            ->paginate(15);

        return view('incomes.index', compact('incomes'));
    }

    public function create()
    {
        $members = Member::whereStatus('active')->orderBy('name')->get();
        $services = Service::orderBy('service_date', 'desc')->get();
        $projects = Project::whereStatus('active')->orderBy('name')->get();
        $types = ['partnership', 'offering', 'project_pledge', 'funeral', 'tuckshop', 'other'];
        $sources = ['manual', 'external'];

        return view('incomes.form', compact('members', 'services', 'projects', 'types', 'sources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'service_id' => 'nullable|exists:services,id',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|in:partnership,offering,project_pledge,funeral,tuckshop,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'source' => 'required|in:manual,external',
        ]);

        Income::create($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income recorded successfully');
    }

    public function edit(Income $income)
    {
        $members = Member::whereStatus('active')->orderBy('name')->get();
        $services = Service::orderBy('service_date', 'desc')->get();
        $projects =Project::whereStatus('active')->orderBy('name')->get();
        $types = ['partnership', 'offering', 'project_pledge', 'funeral', 'tuckshop', 'other'];
        $sources = ['manual', 'external'];

        return view('incomes.form', compact('income', 'members', 'services', 'projects', 'types', 'sources'));
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'service_id' => 'nullable|exists:services,id',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|in:partnership,offering,project_pledge,funeral,tuckshop,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'source' => 'required|in:manual,external',
        ]);

        $income->update($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income updated successfully');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('incomes.index')
            ->with('success', 'Income deleted successfully');
    }
}
