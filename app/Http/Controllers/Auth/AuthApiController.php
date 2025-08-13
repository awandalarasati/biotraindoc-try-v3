<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $cred = $request->validate([
            'login'    => 'required|string', // email ATAU username
            'password' => 'required|string',
        ]);

        $user = User::where('email', $cred['login'])
                    ->orWhere('username', $cred['login'])
                    ->first();

        if (! $user || ! Hash::check($cred['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
            ],
        ]);
    }
}
