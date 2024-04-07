<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventEmailTemplate extends Model
{
    protected $table = 'conf_event_email_template';

    protected $fillable = ['event_id','alias','type'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\EmailTemplateInfo', 'template_id');
    }
}
