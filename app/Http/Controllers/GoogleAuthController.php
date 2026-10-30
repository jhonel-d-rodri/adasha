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
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('login')->with('error', 'La solicitud de acceso expiró. Intente iniciar sesión nuevamente.');
        }

        $user = User::where('google_id', $googleUser->id)->first();

        if (!$user) {
            $user = User::where('email', $googleUser->email)->first();
        }

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->getAvatar(),
                'role' => 'usuario',
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            $user->google_id ??= $googleUser->id;
            $user->avatar = $googleUser->getAvatar();
            $user->save();
        }

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Bienvenido al sistema.');
    }
}
