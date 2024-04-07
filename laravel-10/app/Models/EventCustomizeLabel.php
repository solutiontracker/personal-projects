<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCustomizeLabel extends Model
{

	use SoftDeletes;
	protected $table = 'conf_event_customize_labels';
    protected $fillable = ['id', 'event_id','start_date','end_date'];
	protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('customizeLabelsInfo', 'label_id');
    }

}