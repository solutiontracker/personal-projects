<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class QaParagraph extends Model
{
    protected $table = 'conf_qa_paragraphs';

    protected $fillable = ['event_id', 'serial_number','heading';

    use SoftDeletes;

    protected $dates = ['deleted_at'];

}
