<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model {

    protected $table = 'conf_competition';
    protected $fillable = ['organizer_id','attendee_id','event_id','from_company_name','from_name','title','from_email',
        'from_phone','to_company_name','to_name','to_email','to_phone'];
}