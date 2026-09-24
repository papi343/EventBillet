<?php
namespace App\Services;




class PaymentService {

    public function __construct(){
        Stripe::setApiKey(config('services.stripe.secret'));
    }


    public function createPaymentIntent (Order $order): PaymentIntent{

        return PaymentIntent::create([
            'amount' => (int) ($order->total_amount + 100),
            'currency' => 'mad',
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);
    }
}