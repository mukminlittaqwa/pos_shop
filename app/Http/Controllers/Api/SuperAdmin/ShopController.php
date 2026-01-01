<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('role:super-admin');
    // }

    public function index(Request $request)
    {
        $shops = Shop::when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                     ->paginate(15);
        return response()->json($shops);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:pusat,cabang,retail',
        ]);

        return DB::transaction(function () use ($request) {
            $shop = Shop::create($request->only('name', 'level'));

            $admin = User::create([
                'name' => 'Admin ' . $shop->name,
                'email' => 'admin.' . Str::slug($shop->name) . '.' . $shop->id . '@toko.com',
                'password' => Hash::make('password123'),
                'shop_id' => $shop->id,
            ]);
            $admin->assignRole('admin');

            $kasir = User::create([
                'name' => 'Kasir ' . $shop->name,
                'email' => 'kasir.' . Str::slug($shop->name) . '.' . $shop->id . '@toko.com',
                'password' => Hash::make('password123'),
                'shop_id' => $shop->id,
            ]);
            $kasir->assignRole('kasir');

            return response()->json([
                'message' => 'Toko berhasil dibuat + Admin & Kasir otomatis',
                'shop' => $shop,
                'admin_credentials' => ['email' => $admin->email, 'password' => 'password123'],
                'kasir_credentials' => ['email' => $kasir->email, 'password' => 'password123'],
            ], 201);
        });
    }
}