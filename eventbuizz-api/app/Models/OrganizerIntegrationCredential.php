<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizerIntegrationCredential extends Model
{

    use SoftDeletes;

    protected $table = 'conf_organizer_integration_credentials';

    protected $fillable = ['organizer_id', 'zoom_api_key', 'zoom_api_secret', 'jwt_zoom_api_key', 'jwt_zoom_api_secret'];

    protected $dates = ['deleted_at'];

    public function organizer()
    {
        return $this->belongsTo('\App\Models\Organizer', 'organizer_id', 'id');
    }
}
