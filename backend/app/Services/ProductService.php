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

    public function getProductById($id)
    {
        return Product::findOrFail($id);
    }

    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    public function updateProduct($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        // Catatan: Karena di migration ada onDelete('cascade'), item order yang terkait produk ini akan ikut terhapus.
        // Di sistem aslinya, lebih baik menggunakan SoftDeletes. Tapi untuk tes ini, hard delete sudah cukup.
        return $product->delete();
    }
}
