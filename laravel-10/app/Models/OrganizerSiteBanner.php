<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerSiteBanner extends Model {
	use SoftDeletes;
	protected $table = 'conf_organizersite_banner';
	protected $fillable = ['organizer_id','image','sort_order','status','title','caption'];
	protected $dates = ['deleted_at'];
}
