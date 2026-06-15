<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use Firebase\JWT\JWT;

class WelcomeController extends Controller
{
    public function index()
    {
        $payload = [
            'iss' => 'airo',
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        return view('welcome', [
            'token' => JWT::encode($payload, config('jwt.secret'), config('jwt.algorithm')),
            'currencies' => Currency::cases(),
        ]);
    }

}