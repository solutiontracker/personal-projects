<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerMediaLibrary  extends Model
{

    use SoftDeletes;

    protected $table = 'conf_organizer_media_library';

    protected $fillable = ['id', 'organizer_id','file_name','type'];
    protected $dates = ['deleted_at'];

}