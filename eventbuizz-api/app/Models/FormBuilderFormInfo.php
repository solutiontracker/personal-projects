<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderFormInfo extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_form_info';
    protected $fillable = ['id', "name", 'value', 'language_id', 'form_id'];
}
