<?php

namespace App\Http\Controllers;

use App\Models\Assembly;
use App\Models\FuneralContribution;
use App\Models\Member;
use Illuminate\Http\Request;

class FuneralContributionController extends Controller
{
    public function index()
    {
        $assemblyIds = request()->user()->accessibleAssemblyIds();
        $selectedAssemblyId = request('assembly_id');

        $contributions = FuneralContribution::with(['assembly', 'member'])
            ->whereIn('assembly_id', $assemblyIds)
            ->when($selectedAssemblyId, fn ($query) => $query->where('assembly_id', $selectedAssemblyId))
            ->orderBy('year', 'desc')
            ->paginate(15)
            ->withQueryString();
        $assemblies = Assembly::whereIn('id', $assemblyIds)->orderBy('name')->get();

        return view('funeral-contributions.index', compact('contributions', 'assemblies'));
    }

    public function create()
    {
        $members = Member::whereStatus('active')->orderBy('name')->get();
        $assemblies = Assembly::whereIn('id', request()->user()->accessibleAssemblyIds())->where('status', 'active')->orderBy('name')->get();

        return view('funeral-contributions.form', compact('members', 'assemblies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assembly_id' => 'required|exists:assemblies,id',
            'member_id' => 'required|exists:members,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
        ]);
        abort_unless($request->user()->canAccessAssembly((int) $validated['assembly_id']), 403);

        FuneralContribution::create($validated);

        return redirect()->route('funeral-contributions.index')
            ->with('success', 'Contribution recorded successfully');
    }

    public function edit(FuneralContribution $funeralContribution)
    {
        abort_unless(request()->user()->canAccessAssembly((int) $funeralContribution->assembly_id), 403);

        $members = Member::whereStatus('active')->orderBy('name')->get();
        $assemblies = Assembly::whereIn('id', request()->user()->accessibleAssemblyIds())->where('status', 'active')->orderBy('name')->get();

        return view('funeral-contributions.form', [
            'contribution' => $funeralContribution,
            'members' => $members,
            'assemblies' => $assemblies,
        ]);
    }

    public function update(Request $request, FuneralContribution $funeralContribution)
    {
        $validated = $request->validate([
            'assembly_id' => 'required|exists:assemblies,id',
            'member_id' => 'required|exists:members,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
        ]);
        abort_unless($request->user()->canAccessAssembly((int) $validated['assembly_id']), 403);
        abort_unless($request->user()->canAccessAssembly((int) $funeralContribution->assembly_id), 403);

        $funeralContribution->update($validated);

        return redirect()->route('funeral-contributions.index')
            ->with('success', 'Contribution updated successfully');
    }

    public function destroy(FuneralContribution $funeralContribution)
    {
        abort_unless(request()->user()->hasRole('admin'), 403);
        $funeralContribution->delete();
        return redirect()->route('funeral-contributions.index')
            ->with('success', 'Contribution deleted successfully');
    }
}
