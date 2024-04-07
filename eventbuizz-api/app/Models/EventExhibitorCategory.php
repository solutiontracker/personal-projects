<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventExhibitorCategory extends Model
{
    protected $table = 'conf_event_exhibitor_categories';
    protected $fillable = ['id', 'exhibitor_id', 'category_id', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
