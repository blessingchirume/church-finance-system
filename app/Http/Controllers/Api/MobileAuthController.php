<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        abort_unless($user->canManageFinance(), 403, 'Only finance users can use the mobile app.');

        $plainToken = Str::random(80);
        MobileApiToken::create([
            'user_id' => $user->id,
            'name' => $validated['device_name'] ?? 'Sunday Capture App',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addDays(30),
        ]);

        return response()->json([
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => now()->addDays(30)->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'assemblies' => \App\Models\Assembly::whereIn('id', $user->accessibleAssemblyIds())
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'location']),
        ]);
    }
}
