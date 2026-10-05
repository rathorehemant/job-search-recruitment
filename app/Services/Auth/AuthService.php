<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


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

        /*API Login*/
        if ($request->is('api/*')) {

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw new AuthenticationException('Invalid credentials.');
            }

            $token = $user->createToken('api-token')->accessToken;

            return [
                'success' => true,
                'message' => 'Login successful.',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load('role'),
            ];
        }


        /*Web Login*/
        if (!auth()->attempt($request->only('email', 'password'))) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $request->session()->regenerate();

        $url = $request->session()->pull('url.intended');

        return [
            'success' => true,
            'message' => 'Login successful.',
            'redirect' => $url ?? route('dashboard'),
        ];
    }

    public function logout($request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout successful.');
    }
}
