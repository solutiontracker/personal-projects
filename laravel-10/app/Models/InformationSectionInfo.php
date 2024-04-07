<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformationSectionInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_information_section_info';
    protected $fillable = ['name', 'value', 'section_id', 'language_id', 'status'];

    protected $dates = ['deleted_at'];

    public function information_section(){
        return $this->belongsTo(InformationSection::class,'section_id','id');
    }
}
