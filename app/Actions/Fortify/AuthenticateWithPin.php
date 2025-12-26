<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateWithPin
{
    /**
     * Handle PIN authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string', 'size:4'],
        ]);

        // Check all users with PIN to find matching one
        $user = User::whereNotNull('pin')->get()->first(function ($u) use ($request) {
            return Hash::check($request->pin, $u->pin);
        });

        if (!$user) {
            throw ValidationException::withMessages([
                'pin' => [__('The provided PIN is incorrect.')],
            ]);
        }

        return $user;
    }
}

