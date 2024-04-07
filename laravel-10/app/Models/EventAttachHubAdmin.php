<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventAttachHubAdmin extends Model
{
    protected $table = 'conf_event_attach_hub_admins';

    protected $fillable = ['hub_admin_id', 'event_id', 'type_id', 'type'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function HubAdministrator()
    {
        return $this->belongsTo('\App\Models\HubAdministrator', 'hub_admin_id', 'id');
    }

    public function sponsor()
    {
        return $this->belongsTo('\App\Models\EventSponsor', 'type_id', 'id');
    }

    public function exhibitor()
    {
        return $this->belongsTo('\App\Models\EventExhibitor', 'type_id', 'id');
    }


    public function event()
    {
        return $this->belongsTo('\App\Models\Event', 'event_id', 'id');
    }
}
