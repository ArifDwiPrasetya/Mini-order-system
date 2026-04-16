<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getProducts($search = null, $perPage = 10)
    {
        $query = Product::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->paginate($perPage);
    }
}
