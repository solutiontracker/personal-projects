<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailMarketingTemplate extends Model
{
    use SoftDeletes;
    protected $table = 'conf_email_marketing_template';
    protected $fillable = ['id', 'organizer_id', 'name', 'list_type','folder_id','image','template','created_at','created_by','updated_at','updated_by'];
    protected $dates = ['deleted_at'];


    public function folder()
    {
        return $this->belongsTo('EmailMarketingFolder');
    }

}