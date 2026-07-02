<?php

namespace App\Http\Controllers;

use App\Models\Assembly;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::with('assemblies')->orderBy('name')->paginate(20),
            'roles' => ['admin', 'treasurer', 'viewer'],
            'assemblies' => Assembly::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'treasurer', 'viewer'])],
            'assembly_ids' => ['array'],
            'assembly_ids.*' => ['exists:assemblies,id'],
        ]);

        $user->update(['role' => $validated['role']]);
        $user->assemblies()->sync($validated['assembly_ids'] ?? []);

        return redirect()->route('users.index')->with('success', 'User role updated successfully.');
    }
}
