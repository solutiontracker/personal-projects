<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QAInfo extends Model
{
    protected $table = 'conf_qa_info';
    protected $fillable = ['name','value','qa_id','languages_id','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
