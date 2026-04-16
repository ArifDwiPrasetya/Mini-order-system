<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin System',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123'),
        ]);

        $products = [
            ['name' => 'Indomie Goreng 85g', 'price' => 3100, 'stock' => 250],
            ['name' => 'Aqua Air Mineral 600ml', 'price' => 3500, 'stock' => 120],
            ['name' => 'Teh Botol Sosro 450ml', 'price' => 4500, 'stock' => 85],
            ['name' => 'Kopi Kapal Api Mix 25g', 'price' => 15000, 'stock' => 40],
            ['name' => 'Tolak Angin Sido Muncul 15ml', 'price' => 4000, 'stock' => 150],
            ['name' => 'SilverQueen Cashew 62g', 'price' => 16500, 'stock' => 9],
            ['name' => 'Bimoli Minyak Goreng 2L', 'price' => 38000, 'stock' => 30],
            ['name' => 'Sari Roti Tawar Spesial', 'price' => 17000, 'stock' => 25],
            ['name' => 'Indomilk Kental Manis 370g', 'price' => 12500, 'stock' => 60],
            ['name' => 'Chitato Panggang 68g', 'price' => 11500, 'stock' => 80],
            ['name' => 'Kecap Bango Manis Pouch 550ml', 'price' => 25000, 'stock' => 35],
            ['name' => 'Rinso Anti Noda Liquid 700ml', 'price' => 22000, 'stock' => 50],
            ['name' => 'Pepsodent 190g', 'price' => 14500, 'stock' => 75],
            ['name' => 'Lifebuoy Sabun Mandi 450ml', 'price' => 28000, 'stock' => 65],
            ['name' => 'Kurma Ajwa Premium 500g', 'price' => 85000, 'stock' => 15],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
