<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required']
        ]);

        if (
            Auth::attempt($credentials)
        ) {

            $request->session()->regenerate();

            if (
                Auth::user()->role !== 'admin'
            ) {

                Auth::logout();

                return back()
                    ->withErrors([
                        'email' => 'Unauthorized'
                    ]);
            }

            return redirect()
                ->route('admin.dashboard');
        }

        return back()
            ->withErrors([
                'email' => 'Invalid credentials'
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login');
    }

    public function profile()
    {
        return view('admin.profile.index', [
            'admin' => Auth::user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'password' => ['nullable', 'confirmed', 'min:8']
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email']
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        Auth::update($data);

        return back()->with('success', 'Profile updated successfully');
    }
}