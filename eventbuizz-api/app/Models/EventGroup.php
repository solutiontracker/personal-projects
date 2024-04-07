<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventGroup extends Model {

    protected $table = 'conf_event_groups';
    
    protected $fillable = ['group_id', 'agenda_id','created_at','updated_at'];

	use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasOne('\App\Models\EventGroupInfo', 'group_id', 'id');
    }

	public function parentInfo()
	{
		return $this->belongsTo('\App\Models\EventGroupInfo', 'parent_id', 'group_id');
	}

    public function parent()
    {
        return $this->belongsTo('\App\Models\EventGroup', 'parent_id');
    }

    public function children()
	{
		return $this->hasMany('\App\Models\EventGroup', 'parent_id');
	}

	public function childrenInfo()
	{
		return $this->hasMany('\App\Models\EventGroupInfo', 'group_id');
    }
    
    public function assignAttendeeGroups()
	{
		return $this->hasMany('\App\Models\EventAttendeeGroup', 'group_id');
    }
    
    public function assignAgendaGroups()
    {
        return $this->belongsToMany('\App\Models\Agenda', 'conf_event_agenda_groups', 'group_id', 'agenda_id')->whereNull('conf_event_agenda_groups.deleted_at');
    }
}

