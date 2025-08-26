<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        $user = User::with('role')->where('email', $request->email)->first();
        if (!$user || $user->role->name !== $request->role) {
            return resp(null, null, err('invalid_credentials', 'Email/Role mismatch'), 401);
        }
        $role = $user->role;
        if (!Hash::check($request->password, $role->password)) {
            return resp(null, null, err('invalid_credentials', 'Wrong role password'), 401);
        }
        $token = $user->createToken("api")->plainTextToken;
        return resp(['token' => $token], null);
    }

    public function me(Request $request)
    {
        return resp($request->user());
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return resp(['message' => 'logged_out']);
    }
}
