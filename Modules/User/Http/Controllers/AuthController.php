<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Models\User;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Auth",
 *   description="Authentication and token management"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/v1/auth/login",
     *   tags={"Auth"},
     *   summary="Login with email + role + role-password",
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/AuthLoginRequest")),
     *   @OA\Response(response=200, description="Token issued", @OA\JsonContent(ref="#/components/schemas/AuthLoginResponse")),
     *   @OA\Response(response=401, description="Invalid credentials", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope_Object"))
     * )
     */
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

    /**
     * @OA\Get(
     *   path="/api/v1/auth/me",
     *   tags={"Auth"},
     *   summary="Get current authenticated user",
     *   security={{"bearerAuth": {}}},
     *   @OA\Response(
     *     response=200, description="Current user",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", ref="#/components/schemas/User"),
     *       @OA\Property(property="meta", type="null"),
     *       @OA\Property(property="errors", type="null")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getAuthenticatedUser(Request $request)
    {
        return api_success($request->user()->load('role'));
    }

    /**
     * @OA\Post(
     *   path="/api/v1/auth/logout",
     *   tags={"Auth"},
     *   summary="Logout current token",
     *   security={{"bearerAuth": {}}},
     *   @OA\Response(
     *     response=200, description="Logged out",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", type="object", @OA\Property(property="message", type="string", example="Logged out successfully")),
     *       @OA\Property(property="meta", type="null"),
     *       @OA\Property(property="errors", type="null")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return api_success(['message' => 'Logged out successfully']);
    }
}
