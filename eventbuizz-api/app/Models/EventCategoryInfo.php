<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCategoryInfo extends Model
{
    protected $table = 'conf_event_categories_info';
    protected $fillable = ['name', 'value', 'category_id', 'languages_id', 'id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function scopeOfLanguage($query, $id)
    {
        return $query->where('languages_id', $id);
    }
}
