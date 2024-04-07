<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerSiteLabel extends Model {
	use SoftDeletes;
	protected $table = 'conf_organizersite_labels';
	protected $fillable = ['organizer_id','alias','value'];
	protected $dates = ['deleted_at'];
}
