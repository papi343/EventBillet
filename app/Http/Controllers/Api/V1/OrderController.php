<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Event;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{

    public function __construct(private OrderService $orderService)
    {   
    }

    /**
     * Display a listing of the user's orders.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()
            ->orders()
            ->with(['event', 'tickets'])
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $event = Event::findOrFail($request->validated('event_id'));

        try {
            $order = $this->orderService->createOrder(
                $request->user(),
                $event,
                $request->validated('quantity')
            );

            return  response()->json([
                'success'=> true,
                'message'=>'Order created successfully',
                'order' => new OrderResource($order->load(['event']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success'=> false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, Order $order): JsonResponse|OrderResource
    {
        if ($request->user()->id !== $order->user_id) {
            return response()->json([
                'success'=> false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return  response()->json([
            'success'=> true,
            'message'=>'Order details',  
            'order' => new OrderResource($order->load(['event', 'tickets', 'payment']))
        ], 201);
    }

    /**
     * Cancel an order.
     */
    public function destroy(Request $request, Order $order): JsonResponse
    {
        if ($request->user()->id !== $order->user_id) {
            return response()->json([
                'success'=> false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $this->orderService->cancelOrder($order);

            return response()->json([
                'success'=> true,
                'message' => 'Order cancelled successfully',
                'order' => new OrderResource($order->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=> false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}

