<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSponsorCategory extends Model
{
    protected $table = 'conf_event_sponsor_categories';
    protected $fillable = ['id', 'sponsor_id', 'category_id', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;


}
