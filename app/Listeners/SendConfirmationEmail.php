<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\TicketConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendConfirmationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $event->order->user->notify(new TicketConfirmed($event->order));
    }
}
