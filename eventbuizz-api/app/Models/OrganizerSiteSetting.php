<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerSiteSetting extends Model {
	use SoftDeletes;
    use Observable;
	protected $table = 'conf_organizersite_settings';
	protected $fillable = ['logo', 'organizer_id','aboutus','contactus','show_banner', 'primary_color', 'secondary_color'];
	protected $dates = ['deleted_at'];
}
