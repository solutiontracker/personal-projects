<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignAdditionalFeature extends Model
{
    use SoftDeletes;

    protected $table = 'conf_assign_additional_feature';

    protected $fillable = ['organizer_id', 'name', 'status', 'alias', 'licence_start_date', 'licence_end_date'];

    protected $guarded = [];

    public function organizer()
    {
        return $this->belongsTo('\App\Models\Organizer', 'organizer_id', 'id');
    }
}
