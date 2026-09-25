<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Services\TicketService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateTicketsAfterPayment
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly TicketService $ticketService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $this->ticketService->generateForOrder($event->order);
    }
}
