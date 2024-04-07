<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model {
	use SoftDeletes;
    protected $table = 'conf_labels';
    protected $fillable = ['section_order', 'constant_order', 'alias', 'module_alias', 'parent_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany('\App\Models\LabelInfo', 'label_id');
    }

    public function parent()
    {
        return $this->belongsTo('\App\Models\Label', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('\App\Models\Label', 'parent_id');
    }

    public function childrenInfo()
    {
        return $this->hasMany('\App\Models\LabelInfo', 'label_id');
    }
}