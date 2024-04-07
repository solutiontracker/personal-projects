<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCustomFieldInfo extends Model
{
	protected $table = 'conf_event_custom_fields_info';
	protected $fillable = ['name', 'value', 'custom_field_id', 'languages_id'];

	use SoftDeletes;
	protected $dates = ['deleted_at'];

}
