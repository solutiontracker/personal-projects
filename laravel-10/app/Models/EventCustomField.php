<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCustomField extends Model
{
    protected $table = 'conf_event_custom_fields';
    
    protected $fillable = ['event_id', 'sort_order','parent_id', 'allow_multiple', 'registration_form_id'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('App\Models\EventCustomFieldInfo', 'custom_field_id');
    }

    public function children()
    {
        return $this->hasMany('\App\Models\EventCustomField', 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
