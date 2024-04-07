<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformationSection extends Model
{
    use SoftDeletes;
    protected $table = 'conf_information_sections';
    protected $fillable = ['event_id','sort_order','status','alias','show_in_app','show_in_reg_site'];

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\InformationSectionInfo', 'section_id');
    }

    public function detail_info()
    {
        return $this->hasMany('\App\Models\InformationSectionInfo', 'section_id');
    }
    public function section_pages()
    {
        return $this->hasMany('\App\Models\InformationPage', 'section_id');
    }
}
