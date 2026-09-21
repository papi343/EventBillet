<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
protected $fillable = ['event_id','quantity','total_amount','status'];

 public	function user(): BelongsTo{
return	$this->belongsTo(User::class);
}
public	function	event():	BelongsTo	{
return	$this->belongsTo(Event::class);
}
public	function	tickets():	HasMany	{
return	$this->hasMany(Ticket::class);
}
public	function	payment():	HasOne	{
return	$this->hasOne(Payment::class);
}
}
