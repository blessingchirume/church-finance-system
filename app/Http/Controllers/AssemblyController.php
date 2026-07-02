<?php

namespace App\Http\Controllers;

use App\Models\Assembly;
use Illuminate\Http\Request;

class AssemblyController extends Controller
{
    public function index()
    {
        return view('assemblies.index', [
            'assemblies' => Assembly::withCount('users')->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('assemblies.form', [
            'assembly' => new Assembly(['status' => 'active']),
        ]);
    }

    public function store(Request $request)
    {
        Assembly::create($this->validated($request));

        return redirect()->route('assemblies.index')->with('success', 'Assembly created successfully.');
    }

    public function edit(Assembly $assembly)
    {
        return view('assemblies.form', compact('assembly'));
    }

    public function update(Request $request, Assembly $assembly)
    {
        $assembly->update($this->validated($request, $assembly->id));

        return redirect()->route('assemblies.index')->with('success', 'Assembly updated successfully.');
    }

    public function destroy(Assembly $assembly)
    {
        if ($assembly->incomes()->exists() || $assembly->expenses()->exists() || $assembly->users()->exists()) {
            return back()->with('error', 'This assembly has users or transactions and cannot be deleted.');
        }

        $assembly->delete();

        return redirect()->route('assemblies.index')->with('success', 'Assembly deleted successfully.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:30', 'unique:assemblies,code'.($ignoreId ? ",{$ignoreId}" : '')],
            'location' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }
}
