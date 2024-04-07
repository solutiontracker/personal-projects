<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormPackage extends Model
{
    use SoftDeletes;

    protected $table = 'conf_forms_packages';
    protected $fillable = ['event_id', 'heading', 'sub_heading', 'price','description','registration_form_id','status'];
    protected $dates = ['deleted_at'];
    
}
    
