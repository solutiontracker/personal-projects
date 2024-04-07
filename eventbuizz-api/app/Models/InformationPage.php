<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformationPage extends Model
{
    use SoftDeletes;
    protected $table = 'conf_information_pages';
    protected $fillable = ['sort_order','section_id','parent_id','event_id','page_type','image', 'image_position', 'pdf', 'icon', 'url', 'status','target','website_protocol'];

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\InformationPageInfo', 'page_id');
    }
    public function submenu(){
        return $this->hasMany('\App\Models\InformationPage', 'parent_id','id');
    }
}
