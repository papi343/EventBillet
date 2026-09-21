<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'location', 'price', 'status', 'category_id'];

public	function organizer():	BelongsTo	{
return	$this->belongsTo(User::class,	'organizer_id');
}
public	function	category():	BelongsTo	{
return	$this->belongsTo(Category::class);
}
public	function	tickets():	HasMany	{
return	$this->hasMany(Ticket::class);
}
public	function	orders():	HasMany	{
return	$this->hasMany(Order::class);
}

}
