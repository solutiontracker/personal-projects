<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SponsorNote extends Model
{
    protected $table = 'conf_sponsors_notes';
    protected $fillable = ['event_id', 'sponsor_id', 'attendee_id','notes','created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
