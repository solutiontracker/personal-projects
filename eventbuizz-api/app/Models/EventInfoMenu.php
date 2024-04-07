<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventInfoMenu extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'name' => '',
        'parent_id' => '0',
        'sort_order' => '0'
    ];
    protected $table = 'conf_event_info_menus';
    protected $fillable = ['event_id','parent_id','sort_order','status'];
    protected $dates = ['deleted_at'];

    public function Info()
    {
        return $this->hasMany('\App\Models\EventInfoMenuInfo', 'menu_id');
    }

    /**
     * @api Registration site
     * @param $query
     * @param $event_id
     * @return mixed
     *
     */
    public function scopeOfEvent($query, $event_id)
    {
        return $query->where('event_id', $event_id);
    }
}
