<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userRole = $payload->get('role');
            $userId = $payload->get('sub');

            if ($userRole === 'admin') {
                // Admin bisa melihat semua order
                $orders = Order::with(['user', 'orderItems.product'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Customer hanya bisa melihat order milik sendiri
                $orders = Order::with(['orderItems.product'])
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            return response()->json([
                'message' => 'Orders retrieved successfully',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');

            $order = $this->orderService->createOrder($userId, $request->items);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'error' => 'Failed to create order',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userRole = $payload->get('role');
            $userId = $payload->get('sub');

            // Check authorization
            if ($userRole !== 'admin' && $order->user_id != $userId) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'You can only view your own orders'
                ], 403);
            }

            $order->load(['user', 'orderItems.product']);

            return response()->json([
                'message' => 'Order retrieved successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Order not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        try {
            $order->update(['status' => $request->status]);

            return response()->json([
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update order status',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
