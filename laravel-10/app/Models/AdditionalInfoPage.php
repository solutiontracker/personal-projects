<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalInfoPage extends Model
{
    use SoftDeletes;
    protected $attributes = [
        'sort_order' => '0',
        'image' => '',
        'image_position' => '',
        'pdf' => '',
        'icon' => '',
        'website_protocol' => '',
        'status' => '1',
    ];
    protected $table = 'conf_additional_info_pages';
    protected $fillable = ['sort_order','menu_id','event_id','page_type','image', 'image_position', 'pdf', 'icon', 'url', 'status','website_protocol'];

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\AdditionalInfoPageInfo', 'page_id');
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
