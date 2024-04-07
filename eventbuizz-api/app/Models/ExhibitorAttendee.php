<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExhibitorAttendee extends Model
{
    use SoftDeletes;
    protected $table = 'conf_exhibitors_attendee';
    protected $fillable = ['id', 'exhibitor_id', 'attendee_id'];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
