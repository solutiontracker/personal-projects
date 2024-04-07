<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventDescriptionInfo extends Model {

    use SoftDeletes;
    protected $attributes = [
        'value' => '',
    ];
    protected $table = 'conf_event_description_info';
    protected $fillable = ['name', 'value', 'description_id', 'languages_id'];
    protected $dates = ['deleted_at'];


}
