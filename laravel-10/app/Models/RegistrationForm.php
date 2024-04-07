<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationForm extends Model
{
    protected $table = 'conf_registration_form';

    protected $fillable = ['type_id', 'event_id', 'is_default'];
    
    public $timestamps = false;

    public function attendee_type() {
        return $this->belongsTo(EventAttendeeType::class, 'type_id');
    }
}
