<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    protected $table = 'ticket_replies';
    protected $fillable = ['ticket_id', 'message'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

}
