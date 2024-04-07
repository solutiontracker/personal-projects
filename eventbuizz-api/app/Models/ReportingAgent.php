<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class ReportingAgent extends Authenticatable
{

    use Notifiable, SoftDeletes, HasApiTokens;
    protected $table = 'conf_reporting_agents';
    protected $fillable = ['organizer_id', 'first_name', 'last_name', 'email', 'password', 'address', 'phone', 'status',
    'company', 'title', 'send_password', 'image'];
    protected $dates = ['deleted_at'];

    /**
     * @return mixed
     */
    public function events()
    {
        return $this->belongsToMany('Event','conf_event_reporting_agents','reporting_agent_id','event_id')
            ->whereNull('conf_event_reporting_agents.deleted_at');
    }
}
