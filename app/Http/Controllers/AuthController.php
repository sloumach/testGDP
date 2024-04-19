<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validatedData['password'] = bcrypt($request->password);
        //try catch ici pour vérifier la connexion vers la base est faite  sans probleme
        try {
            $user = User::create($validatedData);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Unable to connect to database'], 500);

        }

        $accessToken = $user->createToken('authToken')->accessToken;

        return response()->json(['user' => $user, 'access_token' => $accessToken]);
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $accessToken = $user->createToken('authToken')->accessToken;
            return response()->json(['user' => $user, 'access_token' => $accessToken]);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401); //pour tous les retourn en toast on les fixe au niveau front
        }
    }
}
