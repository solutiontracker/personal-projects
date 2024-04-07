<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailMarketingFolder extends Model
{
    use SoftDeletes;
    protected $table = 'conf_email_marketing_folder';
    protected $fillable = ['id', 'name' ,'created_at','updated_at'];
    protected $dates = ['deleted_at'];

    public function templates()
    {
        return $this->hasMany('EmailMarketingTemplate','folder_id');
    }

}