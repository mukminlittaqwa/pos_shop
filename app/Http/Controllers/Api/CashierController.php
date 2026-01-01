<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CashierController extends Controller
{

    public function index(Request $request)
    {
        $cashiers = User::role('kasir')
            ->where('shop_id', $request->user()->shop_id)
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                                                ->orWhere('email', 'like', "%{$request->search}%"))
            ->paginate(15);

        return response()->json($cashiers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $kasir = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'shop_id' => $request->user()->shop_id,
        ]);

        $kasir->assignRole('kasir');

        return response()->json([
            'message' => 'Kasir berhasil ditambahkan',
            'kasir' => $kasir,
            'password_plain' => $request->password
        ], 201);
    }

    public function update(Request $request, User $cashier)
    {

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($cashier->id),
                ],
                'password' => 'sometimes|required|min:6',
            ]);

            if ($request->filled('name')) {
                $cashier->name = $request->name;
            }

            if ($request->filled('email')) {
                $cashier->email = $request->email;
            }

            if ($request->filled('password')) {
                $cashier->password = Hash::make($request->password);
            }

            $cashier->save();

            return response()->json([
                'message' => 'Kasir berhasil diupdate oleh Admin',
                'kasir' => $cashier->fresh(),
            ], 200);
        }


    public function destroy(User $cashier)
    {
        // if ($kasir->shop_id !== request()->user()->shop_id || !$cashier->hasRole('kasir')) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $cashier->delete();

        return response()->json(['message' => 'Kasir berhasil dihapus']);
    }
}