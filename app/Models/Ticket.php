<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['order_id', 'event_id', 'price', 'status'];

    public	function order(): BelongsTo{
        return	$this->belongsTo(Order::class);
    }
    public	function event(): BelongsTo{
        return	$this->belongsTo(Event::class);
    }
    public	function user(): BelongsTo{
        return	$this->belongsTo(User::class);
    }
}
