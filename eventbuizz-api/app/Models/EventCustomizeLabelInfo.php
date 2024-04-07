<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCustomizeLabelInfo extends Model
{

	use SoftDeletes;
	protected $table = 'conf_event_customize_label_info';
    protected $fillable = ['id', 'label_id','name','value'];
	protected $dates = ['deleted_at'];

}