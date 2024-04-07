<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectoryFileNote extends Model
{

	use SoftDeletes;
	protected $table = 'conf_directory_files_notes';
    protected $fillable = ['event_id', 'attendee_id', 'file_id', 'notes'];
	protected $dates = ['deleted_at'];

}