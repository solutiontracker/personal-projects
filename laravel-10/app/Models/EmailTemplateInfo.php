<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplateInfo extends Model
{
    protected $attributes = [
        'value' => '',
    ];
    protected $table = 'conf_email_template_info';
    protected $fillable = ['name','value','template_id','languages_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
