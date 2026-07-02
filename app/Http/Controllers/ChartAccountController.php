<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use Illuminate\Http\Request;

class ChartAccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = ChartAccount::with('parent')
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%");
            }))
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        return view('chart-accounts.index', [
            'accounts' => $accounts,
            'types' => ChartAccount::TYPES,
        ]);
    }

    public function create()
    {
        return view('chart-accounts.form', [
            'account' => new ChartAccount(['status' => 'active']),
            'types' => ChartAccount::TYPES,
            'parents' => ChartAccount::where('status', 'active')->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        ChartAccount::create($this->validated($request));

        return redirect()->route('chart-accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(ChartAccount $chartAccount)
    {
        return view('chart-accounts.form', [
            'account' => $chartAccount,
            'types' => ChartAccount::TYPES,
            'parents' => ChartAccount::where('id', '!=', $chartAccount->id)->where('status', 'active')->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, ChartAccount $chartAccount)
    {
        $chartAccount->update($this->validated($request, $chartAccount->id));

        return redirect()->route('chart-accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartAccount $chartAccount)
    {
        abort_unless(request()->user()->hasRole('admin'), 403);

        if ($chartAccount->incomes()->exists() || $chartAccount->expenses()->exists() || $chartAccount->children()->exists()) {
            return back()->with('error', 'This account is in use and cannot be deleted.');
        }

        $chartAccount->delete();

        return redirect()->route('chart-accounts.index')->with('success', 'Account deleted successfully.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:chart_accounts,code'.($ignoreId ? ",{$ignoreId}" : '')],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:'.implode(',', ChartAccount::TYPES)],
            'status' => ['required', 'in:active,inactive'],
            'parent_id' => ['nullable', 'exists:chart_accounts,id'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
