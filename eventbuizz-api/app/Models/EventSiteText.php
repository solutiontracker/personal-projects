<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteText extends Model
{
    protected $table = 'conf_event_site_text';
    
    protected $fillable = ['id', 'section_order', 'constant_order', 'alias', 'event_id', 'status', 'parent_id','label_parent_id', 'created_at', 'updated_at','module_alias', 'deleted_at'];

    use SoftDeletes;

    public function info()
    {
        return $this->hasMany('App\Models\EventSiteTextInfo', 'text_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\EventSiteText', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\EventSiteText', 'parent_id');
    }

    public function childrenInfo()
    {
        return $this->hasMany('App\Models\EventSiteTextInfo', 'text_id');
    }
}
