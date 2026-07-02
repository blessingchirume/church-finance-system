<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::orderBy('name')->paginate(20),
            'roles' => ['admin', 'treasurer', 'viewer'],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'treasurer', 'viewer'])],
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User role updated successfully.');
    }
}
