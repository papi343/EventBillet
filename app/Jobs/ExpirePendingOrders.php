<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Ill
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queueable;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;


class ExpirePendingOrders implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(OrderService $orderService): void
    {
        Order::where('status',OrderStatus::Pending)
              ->where('expire_at', '<', now())
              ->each(function(Order $order) use ($orderService){
                $orderService->expirePending($order);
              });
    }
}
