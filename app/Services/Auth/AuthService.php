<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;


class AuthService
{
    public function login($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $request->session()->regenerate();

        $url = $request->session()->pull('url.intended');

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'redirect' => $url ?? route('dashboard'),
        ]);
    }
}
