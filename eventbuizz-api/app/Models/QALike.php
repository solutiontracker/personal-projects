<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QALike extends Model
{

	use SoftDeletes;
	protected $table = 'conf_qa_likes';
    protected $fillable = ['id', 'event_id', 'attendee_id', 'qa_id'];
	protected $dates = ['deleted_at'];
}