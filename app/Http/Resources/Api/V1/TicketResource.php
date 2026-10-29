<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'event_id' => $this->event_id,
            'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            'price' => (float) $this->price,
            'qr_code' => $this->qr_code,
            'qr_data' => $this->ticket_number ? Crypt::encryptString((string) $this->ticket_number) : null,
            'status' => $this->status?->value ?? $this->status,
            'used_at' => $this->used_at,
            'event' => new EventResource($this->whenLoaded('event')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
        ];
    }
}
