<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\UserRegister;
use App\Services\UserService;
use App\Models\User;
use App\Http\Requests\Api\V1\UserLogin;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function register(UserRegister $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->role = $request->role;
        $user->phone = $request->phone;
        $user->avatar = $request->avatar;
        return $this->userService->create($user);
    }

    public function login(UserLogin $request)
    {
        $user = $this->userService->attempt($request->email, $request->password);
        if (!$user) {
            return response()->json([
                'message' => 'Invalid login credentials',
            ], 401);
        }

        $request->session()->regenerate();
       
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'login successfully',
            'token' => $token,
            'user' => new UserResource($user)
        ], 200);
    }
}
