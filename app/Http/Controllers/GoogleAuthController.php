<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->id)->first();

        if (!$user) {
            $user = User::where('email', $googleUser->email)->first();
        }

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'role' => 'usuario',
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            if (!$user->google_id) {
                $user->google_id = $googleUser->id;
                $user->save();
            }
        }

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Bienvenido al sistema.');
    }
}