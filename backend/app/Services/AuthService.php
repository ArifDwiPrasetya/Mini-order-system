<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new \Exception('Email atau password salah.', 401);
        }

        // Melakukan internal request ke endpoint Passport menggunakan kredensial dari .env
        $request = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => '*',
        ]);

        $response = app()->handle($request);
        $data = json_decode($response->getContent(), true);

        // Menangkap error jika konfigurasi .env belum benar
        if (isset($data['error'])) {
            throw new \Exception('Otentikasi gagal: ' . ($data['message'] ?? $data['error']), 401);
        }

        return $data;
    }

    public function refreshToken(string $refreshToken)
    {
        $request = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
            'scope' => '*',
        ]);

        $response = app()->handle($request);
        $data = json_decode($response->getContent(), true);

        if (isset($data['error'])) {
            throw new \Exception('Refresh token tidak valid atau expired.', 401);
        }

        return $data;
    }

    public function logout()
    {
        $user = Auth::user();
        $user->token()->revoke();
        return true;
    }
}
