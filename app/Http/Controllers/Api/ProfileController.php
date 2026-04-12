<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Update the user's basic profile details.
     */
    public function updateProfileJson(Request $request)
    {
        $user = $request->user();

        // 1. Validate payload
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users,username,' . $user->id],
        ]);

        // 2. Perform the update
        $user->forceFill($validatedData)->save();

        // 3. Return via SOP envelope contract
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user,
        ]);
    }

    /**
     * Update the user's password securely.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // 1. Validate password constraints & match frontend payloads
        $validatedData = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => ['required', 'string'],
        ]);

        // 2. Verify current password matches DB hash
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match our records.'],
            ]);
        }

        // 3. Persist new hashed password
        $user->forceFill([
            'password' => Hash::make($request->new_password),
        ])->save();

        // 4. Return via SOP envelope contract
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
            'data' => $user,
        ]);
    }
}
