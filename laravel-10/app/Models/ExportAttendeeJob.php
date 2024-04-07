<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExportAttendeeJob extends Model
{
	use SoftDeletes;
	protected $table = 'conf_export_attendee_job';
    protected $fillable = ['id', 'event_id', 'key_id','key_name','model_name','email','ids','status','file_name','created_at','updated_at','deleted_at'];
	protected $dates = ['deleted_at'];
}