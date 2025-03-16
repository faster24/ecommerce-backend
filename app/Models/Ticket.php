<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['user_id', 'subject', 'message' , 'status'];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(Customer::class , 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }
}
