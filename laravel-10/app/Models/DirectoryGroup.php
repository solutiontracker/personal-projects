<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectoryGroup extends Model
{

	use SoftDeletes;
	protected $table = 'conf_directory_group';
    protected $fillable = ['directory_id', 'group_id'];
	protected $dates = ['deleted_at'];

}