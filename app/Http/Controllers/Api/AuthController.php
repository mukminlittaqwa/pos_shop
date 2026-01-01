<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()->load('roles'),
        ]);
    }

    public function logout(Request $request)
    {
        auth('api')->logout();
        return response()->json(['message' => 'success logout cuy']);
    }

    public function me(Request $request)
        {
                $user = auth('api')->user();
                $user->load('roles');

                return response()->json([
                    'id'         => $user->id,
                    'shop_id'    => $user->shop_id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'roles'      => $user->roles->pluck('name'), 
                    'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at?->format('Y-m-d H:i:s'),
                ]);
        }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes|required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|nullable|min:6',
        ]);

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'message' => 'Profile berhasil diupdate',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'shop_id' => $user->shop_id,
                'roles' => $user->roles->pluck('name'),
                'updated_at' => $user->updated_at?->format('Y-m-d H:i:s'),
            ]
        ]);
}
}