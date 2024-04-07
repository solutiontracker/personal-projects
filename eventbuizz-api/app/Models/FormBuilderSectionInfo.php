<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderSectionInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_section_info';
    protected $fillable = ['id', 'name', 'value', 'language_id', 'section_id'];
    
}
