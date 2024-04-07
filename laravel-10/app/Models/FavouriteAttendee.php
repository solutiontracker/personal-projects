<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FavouriteAttendee extends Model {

    use SoftDeletes;
    protected $table = 'conf_fovirate_attendees';
    protected $fillable = ['attendee_id', 'fovirate_attendee_id'];
    protected $dates = ['deleted_at'];

}