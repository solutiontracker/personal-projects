<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExhibitorNote extends Model
{
    protected $table = 'conf_exhibitors_notes';
    protected $fillable = ['event_id','exhibitor_id', 'attendee_id','notes','created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
}
