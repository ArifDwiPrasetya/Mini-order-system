<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Jobs\SendOrderNotification;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function getOrders($search = null, $perPage = 10)
    {
        $query = Order::with('user:id,name');

        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getOrderDetail($id)
    {
        return Order::with(['user:id,name', 'items.product'])->findOrFail($id);
    }

    public function createOrder(array $items, $userId)
    {
        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $orderItemsData = [];

            foreach ($items as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                if ($product->stock < $item['qty']) {
                    throw new Exception("Stok untuk produk {$product->name} tidak mencukupi.");
                }

                $subtotal = $product->price * $item['qty'];
                $totalPrice += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                ];

                $product->stock -= $item['qty'];
                $product->save();
            }

            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
            ]);

            foreach ($orderItemsData as $orderItem) {
                $order->items()->create($orderItem);
            }

            DB::commit();

            //Dispatch Job Notifikasi (Async)
            SendOrderNotification::dispatch($order)->afterCommit();

            return $order->load('items');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
