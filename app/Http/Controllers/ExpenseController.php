<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Service;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('service')
            ->latest()
            ->paginate(15);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $services = Service::orderBy('service_date', 'desc')->get();
        $categories = ['worship', 'maintenance', 'outreach', 'administration', 'other'];
        return view('expenses.form', compact('services', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:worship,maintenance,outreach,administration,other',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully');
    }

    public function edit(Expense $expense)
    {
        $services = Service::orderBy('service_date', 'desc')->get();
        $categories = ['worship', 'maintenance', 'outreach', 'administration', 'other'];
        return view('expenses.form', compact('expense', 'services', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'category' => 'required|in:worship,maintenance,outreach,administration,other',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
