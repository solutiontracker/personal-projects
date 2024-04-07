<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelInfo extends Model
{
    protected $table = 'conf_labels_info';
    protected $fillable = ['name', 'value', 'label_id', 'languages_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
