<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Queueable;
use App\Models\Order;
use App\Services\OrderService;
use App\Enums\OrderStatus;

class ExpirePendingOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct()
    {
    }

    public function handle(OrderService $orderService): void
    {
        Order::where('status', OrderStatus::pending)
            ->where('expire_at', '<', now())
            ->each(function (Order $order) use ($orderService) {
                $orderService->expirePending($order);
            });
    }
}
