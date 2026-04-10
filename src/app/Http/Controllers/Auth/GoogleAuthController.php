<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => __('app.google_auth_failed')]);
        }

        // 1. Already linked via google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // 2. Email already registered — link the account
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->google_id = $googleUser->getId();
                if (!$user->avatar_path && $googleUser->getAvatar()) {
                    $user->avatar_path = $googleUser->getAvatar();
                }
                $user->save();
            } else {
                // 3. New user — create account
                $user = User::create([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'username'  => $this->generateUsername($googleUser->getName()),
                    'locale'    => app()->getLocale(),
                    'password'           => null,
                'email_verified_at'  => now(),
                ]);
                $user->google_id   = $googleUser->getId();
                $user->avatar_path = $googleUser->getAvatar();
                $user->save();
            }
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    private function generateUsername(string $name): string
    {
        $base = Str::slug(Str::lower($name), '');
        $base = preg_replace('/[^a-z0-9_]/', '', $base) ?: 'user';
        $base = substr($base, 0, 20);

        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }

        return $username;
    }
}
