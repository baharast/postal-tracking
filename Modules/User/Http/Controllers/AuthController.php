<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        $user = User::with('role')->where('email', $request->safe()->email)->first();
        if (!$user || $user->role->name !== $request->safe()->role) {
            return api_error('invalid_credentials', 'Email and role do not match.', 401);
        }

        if (!Hash::check($request['password'], $user->role->password)) {
            return api_error('invalid_credentials', 'Incorrect role password.', 401);
        }

        $token = $user->createToken('api')->plainTextToken;
        return api_success(['token' => $token]);
    }

    public function getAuthenticatedUser(Request $request)
    {
        return api_success($request->user()->load('role'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return api_success(['message' => 'Logged out successfully']);
    }
}
