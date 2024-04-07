<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormBuilderForm extends Model
{
    use SoftDeletes;
    protected $table = 'conf_form_builder_forms';
    protected $fillable = ['id', "event_id", 'registration_form_id', 'screenshot', 'status', 'active', 'sort_order'];

    public function info()
    {
        return $this->hasMany('\App\Models\FormBuilderFormInfo', 'form_id');
    }
    
    public function sections()
    {
        return $this->hasMany('\App\Models\FormBuilderSection', 'form_builder_form_id')->orderby('sort_order');
    }
    
    
}
