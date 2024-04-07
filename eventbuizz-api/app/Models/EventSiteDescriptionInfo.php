<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSiteDescriptionInfo extends Model {

    use SoftDeletes;
    protected $table = 'conf_eventsite_description_info';
    protected $fillable = ['name', 'value', 'description_id', 'languages_id'];
    protected $dates = ['deleted_at'];


}
