<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketService
{
    /**
     * Decrypt QR data and find the corresponding Ticket.
     */
    public function findTicketFromQrData(string $encryptedData): Ticket
    {
        try {
            $ticketNumber = Crypt::decryptString($encryptedData);
        } catch (DecryptException $e) {
            throw new \Exception('Invalid ticket format');
        }

        return Ticket::with(['event', 'user', 'order'])
            ->where('ticket_number', $ticketNumber)
            ->firstOrFail();
    }

    /**
     * Mark a ticket as used after scanning.
     */
    public function scanTicket(Ticket $ticket): Ticket
    {
        if ($ticket->status === TicketStatus::used || $ticket->status === 'used') {
            throw new \Exception('Ticket has already been used');
        }

        $ticket->update([
            'status' => TicketStatus::used,
            'used_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Generate tickets for a completed order.
     */
    public function generateForOrder(Order $order): void
    {
        for ($i = 0; $i < $order->quantity; $i++) {
            $ticket = $order->tickets()->create([
                'event_id' => $order->event_id,
                'user_id' => $order->user_id,
                'price' => $order->event->price ?? $order->amount,
                'status' => TicketStatus::unused,
                'used_at' => null,
                'ticket_number' => $this->generateTicketNumber(),
            ]);

            $this->generateQrCode($ticket);
        }
    }

    /**
     * Generate QR code for a ticket and save path.
     */
    protected function generateQrCode(Ticket $ticket): void
    {
        $encrypted = Crypt::encryptString($ticket->ticket_number);
        
        $path = 'qr-tickets/' . $ticket->id . '.png';

        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->generate($encrypted);
            Storage::disk('public')->put($path, $qrImage);
        } else {
            // Fallback content if QrCode package is not installed
            Storage::disk('public')->put($path, $encrypted);
        }

        $ticket->update(['qr_code' => $path]);
    }

    /**
     * Generate a unique ticket number.
     */
    protected function generateTicketNumber(): string
    {
        do {
            $number = 'TKT' . now()->format('YmdHis') . strtolower(Str::random(6));
        } while (Ticket::where('ticket_number', $number)->exists());

        return $number;
    }
}