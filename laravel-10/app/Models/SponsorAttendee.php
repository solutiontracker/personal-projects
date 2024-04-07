<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorAttendee extends Model
{
    use SoftDeletes;
    protected $table = 'conf_sponsors_attendee';
    protected $fillable = ['id', 'sponsor_id', 'attendee_id'];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
