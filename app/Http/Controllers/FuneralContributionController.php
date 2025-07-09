<?php

namespace App\Http\Controllers;

use App\Models\FuneralContribution;
use App\Models\Member;
use Illuminate\Http\Request;

class FuneralContributionController extends Controller
{
    public function index()
    {
        $contributions = FuneralContribution::with('member')
            ->orderBy('year', 'desc')
            ->paginate(15);
        return view('funeral-contributions.index', compact('contributions'));
    }

    public function create()
    {
        $members = Member::active()->orderBy('name')->get();
        return view('funeral-contributions.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
        ]);

        FuneralContribution::create($validated);

        return redirect()->route('funeral-contributions.index')
            ->with('success', 'Contribution recorded successfully');
    }

    public function edit(FuneralContribution $funeralContribution)
    {
        $members = Member::active()->orderBy('name')->get();
        return view('funeral-contributions.edit', [
            'contribution' => $funeralContribution,
            'members' => $members,
        ]);
    }

    public function update(Request $request, FuneralContribution $funeralContribution)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
        ]);

        $funeralContribution->update($validated);

        return redirect()->route('funeral-contributions.index')
            ->with('success', 'Contribution updated successfully');
    }

    public function destroy(FuneralContribution $funeralContribution)
    {
        $funeralContribution->delete();
        return redirect()->route('funeral-contributions.index')
            ->with('success', 'Contribution deleted successfully');
    }
}
