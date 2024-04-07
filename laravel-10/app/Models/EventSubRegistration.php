<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSubRegistration extends Model
{
	use SoftDeletes;

	protected $table = 'conf_event_sub_registration';

    protected $fillable = ['event_id', 'status', 'registration_form_id'];

	protected $dates = ['deleted_at'];

	public function question()
	{
		return $this->hasMany('\App\Models\EventSubRegistrationQuestion', 'sub_registration_id')
		->orderBy('sort_order')
		->orderBy('id', 'ASC');
	}

	public function results()
	{
		return $this->hasMany('\App\Models\EventSubRegistrationResult', 'sub_registration_id');
	}
	
}