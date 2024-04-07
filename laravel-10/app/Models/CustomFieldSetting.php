<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomFieldSetting extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table= "conf_custom_field_settings";

    protected $fillable= [
        'custom_field_mode',
        'event_id'
    ];

}
