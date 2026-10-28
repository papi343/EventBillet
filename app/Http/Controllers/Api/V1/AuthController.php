<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\UserRegister;
use App\Services\UserService;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserLogin;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function register(UserRegister $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->role = $request->role ?? 'Participant';
        $user->phone = $request->phone;

        if ($request->hasFile('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        } else {
            $user->avatar = $request->avatar;
        }

        $createdUser = $this->userService->create($user);
        $token = $createdUser->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => new UserResource($createdUser),
        ], 201);
    }

    public function login(UserLogin $request)
    {
        $user = $this->userService->attempt($request->email, $request->password);
        if (!$user) {
            return response()->json([
                'message' => 'Invalid login credentials',
            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'login successfully',
            'token' => $token,
            'user' => new UserResource($user),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}
