<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function checkout(Order $order)
    {
        try {
            $intent = $this->paymentService->createPaymentIntent($order);
            return response()->json([
                'success' => true,
                'order' => new OrderResource($order->load(['event'])),
                'clientSecret' => $intent->client_secret,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'order' => new OrderResource($order->load(['event'])),
                'clientSecret' => 'pi_demo_secret_' . $order->id,
                'demo_mode' => true,
            ], 200);
        }
    }

    public function confirm(Request $request, Order $order)
    {
        if ($order->status === OrderStatus::paid) {
            return response()->json([
                'success' => true,
                'message' => 'Order already paid',
                'order' => new OrderResource($order->load(['event', 'tickets', 'payment'])),
            ], 200);
        }

        $order->payment()->create([
            'status' => PaymentStatus::paid->value,
            'currency' => 'mad',
            'amount' => $order->total_amount,
            'payment_method' => $request->input('payment_method', 'card'),
            'stripe_payment_id' => $request->input('stripe_payment_id', 'pi_' . uniqid()),
        ]);

        $order->update([
            'status' => OrderStatus::paid,
        ]);

        OrderPaid::dispatch($order);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed and tickets generated',
            'order' => new OrderResource($order->fresh()->load(['event', 'tickets', 'payment'])),
        ], 200);
    }
}
