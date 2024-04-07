<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectoryFile extends Model
{

    protected $table = 'conf_directory_files';
    protected $fillable = ['id', 'parent_id', 'directory_id', 'organizer_id', 'file_size', 'path', 'start_date',
        'start_time', 'sort_order', 's3', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\FileInfo', 'file_id');
    }

    public function directories()
    {
        return $this->belongsTo('\App\Models\Directory', 'directory_id', 'id');
    }

    public function file_notes()
    {
        return $this->hasOne('\App\Models\DirectoryFileNote', 'file_id', 'id');
    }
}
