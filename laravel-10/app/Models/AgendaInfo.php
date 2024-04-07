<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaInfo extends Model {

	protected $table = 'conf_agenda_info';
	protected $fillable = ['name','value','agenda_id','languages_id','status'];

    public function scopeOfLanguage($query, $id)
    {
        return $query->where('languages_id', $id);
    }
}