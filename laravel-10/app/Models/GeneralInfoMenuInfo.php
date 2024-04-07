<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralInfoMenuInfo extends Model
{
    protected $table = 'conf_general_info_menu_info';
    protected $fillable = ['name', 'value', 'menu_id', 'languages_id', 'status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];


}
