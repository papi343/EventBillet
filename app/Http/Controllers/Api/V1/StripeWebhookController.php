<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->headers->get('stripe-signature'),
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown event type',
                    ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature or webhook error',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook received',
        ]);
    }

    public function handlePaymentIntentSucceeded($paymentIntent)
    {
        $order = Order::findOrFail($paymentIntent->metadata->order_id);

        if ($order->status === OrderStatus::paid) {
            return;
        }

        $order->payment()->create([
            'status' => PaymentStatus::paid->value,
            'currency' => $paymentIntent->currency ?? 'mad',
            'amount' => ($paymentIntent->amount ?? 0) / 100,
            'payment_method' => 'stripe',
            'stripe_payment_id' => $paymentIntent->id,
        ]);

        $order->update([
            'status' => OrderStatus::paid,
        ]);

        OrderPaid::dispatch($order);

        return response()->json([
            'success' => true,
            'message' => 'Payment intent succeeded',
        ]);
    }
}
