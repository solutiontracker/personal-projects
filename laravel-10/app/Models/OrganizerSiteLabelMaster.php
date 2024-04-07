<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerSiteLabelMaster extends Model {
	use SoftDeletes;
	protected $table = 'conf_organizersite_label_master';
	protected $fillable = ['alias', 'value'];
	protected $dates = ['deleted_at'];
}
