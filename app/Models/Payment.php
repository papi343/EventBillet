<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [ 'order_id', 'amount', 'status', 'payment_method', 'currency']


    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function order():BelongsTo{
        return $this->belongsTo(Order::class);
    }
    
}
