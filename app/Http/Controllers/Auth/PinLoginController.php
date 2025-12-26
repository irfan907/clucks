<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\AuthenticateWithPin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PinLoginController extends Controller
{
    /**
     * Show the PIN login form.
     */
    public function show()
    {
        return view('livewire.auth.login-pin');
    }

    /**
     * Handle PIN authentication.
     */
    public function store(Request $request, AuthenticateWithPin $authenticateWithPin)
    {
        try {
            $user = $authenticateWithPin($request);

            Auth::login($user, $request->boolean('remember'));

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}

