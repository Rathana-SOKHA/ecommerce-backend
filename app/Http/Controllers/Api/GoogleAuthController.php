<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google Login
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Handle Google Callback
     */
    public function callback()
    {
        try {

            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            // 1. Try find by Google ID first (BEST PRACTICE)
            $user = User::where('google_id', $googleUser->id)->first();

            // 2. If not found, try email (existing normal account)
            if (!$user) {
                $user = User::where('email', $googleUser->email)->first();
            }

            // 3. If user still not found → REGISTER NEW USER
            if (!$user) {

                $user = User::create([
                    'name'      => $googleUser->name,
                    'email'     => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar,
                    'password'  => Hash::make(Str::random(32)),
                    'role'      => 'customer',
                ]);
            } else {

                // 4. Update Google info if existing user logs in with Google
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar,
                ]);
            }

            // 5. Create token
            $token = $user->createToken('google-login')->plainTextToken;

            // 6. Redirect to Vue
            return redirect(
                env('FRONTEND_URL')
                    . '/google/callback?token=' . urlencode($token)
            );
        } catch (\Exception $e) {

            return redirect(
                env('FRONTEND_URL')
                    . '/login?error=' . urlencode('Google login failed')
            );
        }
    }
}
