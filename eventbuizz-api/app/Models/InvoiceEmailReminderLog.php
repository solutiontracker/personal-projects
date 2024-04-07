<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceEmailReminderLog extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_email_reminder_log';

    protected $fillable = ['event_id', 'order_id', 'attendee_id', 'status', 'is_new_flow'];
    
}
