<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class BillingOrderAttendee extends Model
{

    protected $table = 'conf_billing_order_attendees';

    protected $fillable = ['order_id', 'attendee_id', 'event_qty', 'event_discount', 'subscriber_ids', 'accept_foods_allergies', 'accept_gdpr', 'cbkterms', 'status', 'attendee_type', 'registration_form_id', 'member_number'];

    use SoftDeletes;

    /**
     * Set the subscriber_ids
     *
     * @param  string  $value
     * @return void
     */
    public function setSubscriberIdsAttribute($value)
    {
        $this->attributes['subscriber_ids'] = implode(",", (array)$value);
    }

    /**
     * Get the subscriber_ids.
     *
     * @param  string  $value
     * @return string
     */
    public function getSubscriberIdsAttribute($value)
    {
        return $value ? array_map('intval', explode(',', $value)) : [];
    }

    public function attendee_detail()
    {
        return $this->belongsTo('\App\Models\Attendee', 'attendee_id', 'id');
    }
}
