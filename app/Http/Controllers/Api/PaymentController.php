<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    

    public function __construct( private PaymentService $paymentService){}


    public function checkout(Order $order)
    {

        $intent = $this->paymentService->createPaymentIntent($order);

        return response()->json([
            'success' => true,
            'order' =>$order,
            'clientSecret' => $intent->client_secret,
        ]200);
    }
}
