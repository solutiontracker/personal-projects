<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Integration extends Model
{
    use SoftDeletes;

    protected $table = 'conf_integrations';
    protected $fillable = ['name', 'alias', 'logo'];
    protected $dates = ['deleted_at'];


    public function rules()
    {
       return $this->hasMany(IntegrationRule::class, 'integration_id');
    }

    public function organizers()
    {
        return $this->belongsToMany(Organizer::class, 'conf_organizer_integrations', 'integration_id', 'organizer_id');
    }
}
