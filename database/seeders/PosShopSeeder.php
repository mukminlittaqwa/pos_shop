<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PosShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@dazo.com',
            'password' => Hash::make('password'),
            'shop_id' => null,
        ]);
        $superAdmin->assignRole('super-admin');

        $shops = [
            [
                'name' => 'Toko Pusat Maju Jaya',
                'level' => 'pusat',
            ],
            [
                'name' => 'Toko Cabang Sejahtera',
                'level' => 'cabang',
            ],
        ];

        $products = [
            ['name' => 'Indomie Goreng', 'price' => 3500, 'description' => 'Mie instan goreng'],
            ['name' => 'Aqua Botol 600ml', 'price' => 4000, 'description' => 'Air mineral'],
            ['name' => 'Kopi Good Day', 'price' => 2000, 'description' => 'Kopi sachet'],
            ['name' => 'Sabun Lifebuoy', 'price' => 5000, 'description' => 'Sabun batang'],
            ['name' => 'Rokok Surya 12', 'price' => 25000, 'description' => 'Rokok kretek'],
        ];

        foreach ($shops as $shopData) {
            $shop = Shop::create($shopData);

            $admin = User::create([
                'name' => 'Admin ' . $shop->name,
                'email' => 'admin.' . Str::slug($shop->name) . '@toko.com',
                'password' => Hash::make('password123'),
                'shop_id' => $shop->id,
            ]);
            $admin->assignRole('admin');

            $kasir = User::create([
                'name' => 'Kasir ' . $shop->name,
                'email' => 'kasir.' . Str::slug($shop->name) . '@toko.com',
                'password' => Hash::make('password123'),
                'shop_id' => $shop->id,
            ]);
            $kasir->assignRole('kasir');

            foreach ($products as $prod) {
                Product::create([
                    'shop_id' => $shop->id,
                    'name' => $prod['name'],
                    'description' => $prod['description'],
                    'price' => $prod['price'],
                ]);
            }

            for ($i = 1; $i <= 3; $i++) {
                $total = 0;
                $items = [];

                $selectedProducts = Product::where('shop_id', $shop->id)->inRandomOrder()->limit(rand(2, 4))->get();

                foreach ($selectedProducts as $prod) {
                    $quantity = rand(1, 5);
                    $subtotal = $prod->price * $quantity;
                    $total += $subtotal;

                    $items[] = [
                        'product_id' => $prod->id,
                        'quantity' => $quantity,
                        'price' => $prod->price,
                    ];
                }

                $sale = Sale::create([
                    'shop_id' => $shop->id,
                    'user_id' => $kasir->id,
                    'total' => $total,
                ]);

                foreach ($items as $item) {
                    SaleItem::create(array_merge($item, ['sale_id' => $sale->id]));
                }

                Payment::create([
                    'sale_id' => $sale->id,
                    'amount_paid' => $total + rand(5000, 20000),
                    'method' => ['cash', 'transfer'][rand(0,1)],
                    'change' => $i % 2 == 0 ? rand(1000, 5000) : null,
                ]);
            }
        }

        $this->command->info('Seeder selesai di buat gan:');
        $this->command->info('- Super Admin: super@dazo.com / password');
        $this->command->info('- 2 Toko dengan Admin & Kasir (password123)');
        $this->command->info('- 5 produk per toko');
        $this->command->info('- 3 transaksi per toko');
    }
}
