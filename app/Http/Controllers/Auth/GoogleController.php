<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;

class GoogleController extends Controller
{
    // Redirect to Google
    public function redirect()
    {
        return Socialite::driver('google')
    ->scopes([
        'openid',
        'profile',
        'email',
        'https://www.googleapis.com/auth/drive.file'
    ])
    ->redirect();
    }

    // Handle callback
    public function callback()
{
    $user = Socialite::driver('google')->stateless()->user();

    session(['user' => [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $user->avatar,
        'token' => $user->token,
    ]]);

    return redirect('/dashboard');
}


public function logout()
{
    session()->forget('user');
    session()->flush();

    return redirect('/auth/google');
}
}