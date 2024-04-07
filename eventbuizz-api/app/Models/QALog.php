<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QALog extends Model
{
    use SoftDeletes;
    protected $table = 'conf_qa_log';
    protected $dates = ['deleted_at'];
}
