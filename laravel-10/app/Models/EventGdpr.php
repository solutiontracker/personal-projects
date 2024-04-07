<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGdpr extends Model {

    protected $table = 'conf_event_gdpr';
    protected $fillable = ['event_id','subject','inline_text','description'];

}