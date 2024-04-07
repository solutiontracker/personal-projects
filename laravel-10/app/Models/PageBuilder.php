<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class PageBuilder extends Model
{
    use SoftDeletes;
    protected $table = 'conf_page_builder_pages';
    protected $fillable = ['page_id','assets','components','css	','html','styles'];  
    protected $dates = ['deleted_at'];
}
