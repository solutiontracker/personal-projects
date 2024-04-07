<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QASettingTemplate extends Model
{
    protected $table = 'conf_qa_setting_template';
    protected $fillable = ['event_id', 'template', 'detail', 'status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}




