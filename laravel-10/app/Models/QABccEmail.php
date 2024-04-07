<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QABccEmail extends Model
{
    protected $table = 'conf_qa_bcc_emails';
    protected $fillable = ['event_id', 'program_id', 'bcc_email'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
