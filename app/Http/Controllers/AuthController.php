<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Website member login (Google OAuth via Laravel Socialite).
 */
class AuthController extends Controller
{
    public function googleRedirect()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->withErrors(['google' => 'Google login is not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env']);
        }

        return Socialite::driver('google')->redirect();
    }

    public function googleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors(['google' => 'Google sign in was cancelled.']);
        }

        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors(['google' => 'Google sign in failed, please try again.']);
        }

        $user = User::where('google_id', $google->getId())->first()
            ?? User::where('email', $google->getEmail())->first();

        if (! $user) {
            $user = new User();
            $user->username = $this->uniqueUsername($google->getNickname() ?: Str::before((string) $google->getEmail(), '@') ?: $google->getName());
            $user->role = 'user';
            $user->email_verified_at = now();
        }

        $user->google_id = $google->getId();
        $user->name = $user->name ?: $google->getName();
        $user->email = $user->email ?: $google->getEmail();
        $user->avatar = $google->getAvatar();
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', 'Welcome to the Cannaverse, '.($user->name ?: $user->username).'!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been signed out.');
    }

    private function uniqueUsername(?string $base): string
    {
        $base = Str::of((string) $base)->ascii()->lower()->replaceMatches('/[^a-z0-9_]/', '')->limit(16, '')->toString() ?: 'member';
        $name = $base;
        while (User::withTrashed()->where('username', $name)->exists()) {
            $name = $base.random_int(100, 9999);
        }

        return $name;
    }
}
