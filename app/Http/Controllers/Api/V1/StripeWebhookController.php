<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    //

    public function handle(Request $request){

        try{
            $evnt = webhook::constructEvent(
                $request->getContent(),
                $request->headers->get('stripe-signature'),
                config('services.stripe.webhook_secret')
            );
            switch($event->type){
                case 'payment_intent.succeeded' : 
                $this->handlePaymentIntentSucceeded($event);
                break;
                
                default: 
                return response()->json([
                    'success' => false,
                    'message' => 'Unknown event type']);
            }
            
        }catch (\Stripe\Exception\SignatureVerificationException $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature']);
        }
        return response()->json([
            'success' => true,
            'message' => 'Webhook received']);
    }

    public function handlePaymentIntentSucceeded($paymentIntent){

        $order = Order::findOrFail($paymentIntent->metadata->order_id);
         
         if($order->status === OrderStatus::paid){
            return;
         }
         $order->payment()->create([
            'status' => PaymentStatus::paid,
            'currency' => $paymentIntent->currency,
            'amount' => $paymentIntent->amount /100,
            'stripe_payment_id' => $paymentIntent->id,
         ]);

         $order->update([
            'status' => OrderStatus::paid,
         ]);
        OrderPaid::dispatch($order); //Dispatch event to generate tickets
        
        return response()->json([
            'success' => true,
            'message' => 'Payment intent succeeded']);
    }
}
