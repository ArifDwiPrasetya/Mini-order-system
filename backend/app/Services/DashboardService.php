<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Concurrency;

class DashboardService
{
    public function getSummary()
    {
        // Menggunakan Concurrency::run() untuk mengeksekusi query secara paralel
        $results = Concurrency::run([
            fn() => Order::count(),
            fn() => Product::count(),
            fn() => User::count(),
        ]);

        return [
            'total_orders' => $results[0],
            'total_products' => $results[1],
            'total_users' => $results[2],
        ];
    }
}
