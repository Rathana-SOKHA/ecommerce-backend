<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * View Profile
     */
    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Update Name & Email
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ]
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->fresh()
        ]);
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        if (! Hash::check(
            $request->current_password,
            $user->password
        )) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        // delete tokens when changed password
        // $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}