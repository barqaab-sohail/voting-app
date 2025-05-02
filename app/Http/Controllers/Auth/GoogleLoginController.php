<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find user by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                return redirect('/login')->with('error', 'No account found with this Google email. Please contact the administrator.');
            }

            // Check if Google login is allowed for this user
            if (!in_array($user->login_method, ['google', 'both'])) {
                return redirect('/login')->with('error', 'Google login is not enabled for your account.');
            }

            // Verify the Google ID matches the allowed one
            if ($user->allowed_google_id && $googleUser->getId() !== $user->allowed_google_id) {
                return redirect('/login')->with('error', 'This Google account is not authorized for login.');
            }

            // Update user's Google ID if not set
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user, true);
            return redirect()->intended('/home');
        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}
