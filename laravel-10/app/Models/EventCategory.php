<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class EventCategory extends Model
{
    protected $table = 'conf_event_categories';
    protected $fillable = ['event_id', 'color', 'parent_id','cat_type', 'sort_order', 'status', 'id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function info(){
        return $this->hasMany(EventCategoryInfo::class, 'category_id');
    }
}
