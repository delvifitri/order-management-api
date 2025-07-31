<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(int $userId, array $items): Order
    {
        return DB::transaction(function () use ($userId, $items) {
            $this->validateStockAvailability($items);

            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => 0,
                'status' => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                $totalAmount += $orderItem->subtotal;

                $product->decreaseStock($item['quantity']);
            }

            $order->update(['total_amount' => $totalAmount]);

            return $order->load(['user', 'orderItems.product']);
        });
    }

    private function validateStockAvailability(array $items): void
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product || !$product->is_active) {
                throw new \Exception("Product {$item['product_id']} is not available");
            }
            
            if ($product->stock < $item['quantity']) {
                throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}");
            }
        }
    }
}