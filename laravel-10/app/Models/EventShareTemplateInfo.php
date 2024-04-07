<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventShareTemplateInfo extends Model {
    use SoftDeletes;
    protected $table = 'conf_event_share_template_info';
    protected $fillable = ['name','value','template_id','languages_id','status'];
    protected $dates = ['deleted_at'];
}
