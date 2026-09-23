<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\StripeService;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderService
{

    public function createOrder(User $user, Event $event, int $quantity): Order {
        return DB::transaction(function() use ($user, $event, $quantity) {
            $lockedEvent = Event::lockForUpdate()->findOrFail($event->id);
            if ($lockedEvent->available_seats < $quantity) {
                throw new \Exception('Not enough seats available');
            }
            $order = $lockedEvent->orders()->create([
                'user_id' => $user->id,
                'quantity' => $quantity,
                'total_amount' => $event->price * $quantity,
                'status' => OrderStatus::Pending,
                'expire_at' => now()->addMinutes(10),
            ]);

            $lockedEvent->decrement('available_seats', $quantity);
            return $order;
        });
    }

    public function cancelOrder(Order $order): void {
        DB::transaction(function() use ($order) {
            if ($order->status === OrderStatus::Paid) {
                $order->event()->increment('available_seats', $order->quantity);
            }
            $order->update(['status' => OrderStatus::Refunded]);
        });
    }

    public function expirePending(Order $order): void {
        DB::transaction(function() use ($order) {
            // utiliser lockForUpdate pour éviter que l'utilisateur termine de payer au même moment
            // que le job tente d'annuler
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status !== OrderStatus::Pending) {
                return;
            }
            $lockedOrder->event()->increment('available_seats', $lockedOrder->quantity);
            $lockedOrder->update(['status' => OrderStatus::Failed]);
        });
    }
}