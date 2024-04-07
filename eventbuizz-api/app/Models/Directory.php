<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Directory extends Model
{
    protected $attributes = [
        'sort_order' => '0',
    ];
    protected $table = 'conf_directory';
    protected $fillable = ['id', 'parent_id', 'other', 'agenda_id', 'event_id', 'speaker_id', 'sponsor_id', 'exhibitor_id',
        'sort_order', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\DirectoryInfo', 'directory_id');
    }

    public function files()
    {
        return $this->hasMany('\App\Models\DirectoryFile', 'directory_id');
    }

    public function children()
    {
        return $this->hasMany('\App\Models\Directory', 'parent_id');
    }

    public function childrenRecursiveWithFiles()
    {
        return $this->children()->with(['childrenRecursiveWithFiles.files']);
    }

    public function groups()
    {
        return $this->belongsToMany(EventGroup::class, 'conf_directory_group', 'directory_id', 'group_id')->whereNull('conf_directory_group.deleted_at');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($directory) {
            $directory->info()->delete();
            $directory->files()->each(function ($file) {
                if ($file->s3 == 1) {
                    deleteObject('assets/directory/' . $file->path);
                } else {
                    deleteFile(config('cdn.cdn_upload_path') . 'assets/directory/' . $file->path);
                }
                $file->delete();
            });
        });
    }

    public function event(){
        return $this->belongsTo(Event::class);
    }
    
    public function Agenda(){
        return $this->belongsTo(Agenda::class,'agenda_id','id');
        
    }
}
