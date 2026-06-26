<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@nusamind.test'],
            [
                'name' => 'Admin Nusamind',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
        $this->command->info("Admin: admin@nusamind.test / password");

        $user = User::firstOrCreate(
            ['email' => 'user@nusamind.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
            ]
        );
        $this->command->info("User: user@nusamind.test / password");

        $category = Category::inRandomOrder()->first();

        if ($category) {
            $business = Business::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name' => 'Toko Berkah Jaya',
                    'category_id' => $category->id,
                    'city' => 'Surabaya',
                    'description' => 'Toko sembako dan kebutuhan sehari-hari di Surabaya Barat. Melayani pembelian grosir dan eceran.',
                ]
            );
            $this->command->info("Bisnis: Toko Berkah Jaya (Surabaya)");

            $products = [
                ['name' => 'Beras Pandan Wangi 5kg', 'price' => 75000, 'stock' => 50],
                ['name' => 'Minyak Goreng 2L', 'price' => 35000, 'stock' => 30],
                ['name' => 'Gula Pasir 1kg', 'price' => 16000, 'stock' => 100],
                ['name' => 'Kopi Kapal Api 20sachet', 'price' => 12000, 'stock' => 5],
                ['name' => 'Telur Ayam 1kg', 'price' => 28000, 'stock' => 20],
            ];

            foreach ($products as $p) {
                Product::firstOrCreate(
                    ['business_id' => $business->id, 'name' => $p['name']],
                    $p
                );
            }
            $this->command->info('5 produk dibuat');

            $transactions = [
                ['type' => 'pemasukan', 'item_name' => 'Beras Pandan Wangi 5kg', 'amount' => 375000, 'quantity' => 5, 'transaction_date' => Carbon::yesterday()],
                ['type' => 'pengeluaran', 'item_name' => 'Stok Barang', 'amount' => 500000, 'quantity' => 1, 'transaction_date' => Carbon::yesterday()],
                ['type' => 'pemasukan', 'item_name' => 'Minyak Goreng 2L', 'amount' => 105000, 'quantity' => 3, 'transaction_date' => Carbon::today()->subDays(2)],
                ['type' => 'pemasukan', 'item_name' => 'Telur Ayam 1kg', 'amount' => 56000, 'quantity' => 2, 'transaction_date' => Carbon::today()->subDays(2)],
                ['type' => 'pengeluaran', 'item_name' => 'Listrik', 'amount' => 250000, 'quantity' => 1, 'transaction_date' => Carbon::today()->subDays(3)],
                ['type' => 'pemasukan', 'item_name' => 'Gula Pasir 1kg', 'amount' => 32000, 'quantity' => 2, 'transaction_date' => Carbon::today()->subDays(3)],
                ['type' => 'pemasukan', 'item_name' => 'Kopi Kapal Api 20sachet', 'amount' => 24000, 'quantity' => 2, 'transaction_date' => Carbon::today()->subDays(4)],
            ];

            foreach ($transactions as $t) {
                Transaction::create(array_merge($t, ['user_id' => $user->id, 'source' => 'manual']));
            }
            $this->command->info('7 transaksi dibuat');
        }
    }
}
