<?php

namespace App\Models;

use App\Traits\Observable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintSetting extends Model
{
    protected $attributes = [
        'dropdown' => '',
        'sub_category' => '',
        'auto_select_subcategory' => '',
        'browser' => '',
    ];
    use Observable;
    protected $table = 'conf_print_settings';
    protected $fillable = ['event_id', 'active', 'username', 'password', 'dropdown', 'sub_category', 'auto_select_subcategory', 'browser', 'print_message'];

}
