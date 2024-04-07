<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerSiteBannerSetting extends Model {

	use SoftDeletes;
	protected $table = 'conf_organizersite_banner_settings';
	protected $fillable = ['organizer_id','title','caption','register_button','bottom_bar'];
	protected $dates = ['deleted_at'];
}
