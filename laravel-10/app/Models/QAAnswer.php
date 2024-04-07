<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QAAnswer extends Model
{
    protected $table = 'conf_qa_answers';
    protected $fillable = ['answer','sender_id','qa_id','is_admin'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
