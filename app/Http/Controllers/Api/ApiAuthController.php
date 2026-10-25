<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    // DMS: documents:read
    // AMS: accounting:read
    private const LOGIN_SCOPE = 'accounting:read';

    public function login(Request $request)
    {
        // STEP 1: Validate request
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // STEP 2: Find user in local database
        $user = User::where('email', $validated['email'])
            ->first();

        // STEP 3: Check password
        if (!$user || !Hash::check(
            $validated['password'],
            $user->password
        )) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // STEP 4: Generate Laravel Passport token
        $token = $user->createToken(
            'API Login Token',
            [self::LOGIN_SCOPE]
        );

        // STEP 5: Return access token
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the current Passport token
        $request->user('api')->token()->revoke();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
