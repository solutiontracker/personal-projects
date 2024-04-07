<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeBillingItemEmail extends Model
{

    use SoftDeletes;
    protected $table = 'conf_attendee_billing_item_emails';
    protected $fillable = ['event_id', 'attendee_id', 'attendee_success_email', 'invite_email'];
    protected $dates = ['deleted_at'];

}