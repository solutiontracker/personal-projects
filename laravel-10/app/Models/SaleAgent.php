<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;


class SaleAgent extends Authenticatable
{

    use SoftDeletes, HasApiTokens, SendsPasswordResetEmails, Notifiable;
    protected $table = 'conf_sale_agents';
    protected $fillable = ['organizer_id', 'first_name', 'last_name', 'email', 'password', 'address', 'phone', 'status',
    'company', 'title', 'send_password', 'image'];

    protected $dates = ['deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    public function events()
    {
        return $this->belongsToMany(Event::class, 'conf_event_sale_agents', 'sale_agent_id', 'event_id')
            ->whereNull('conf_event_sale_agents.deleted_at');
    }


    public function orders()
    {
        return $this->hasMany(BillingOrder::class, 'sale_agent_id')
            ->whereNull('conf_billing_orders.deleted_at')
            ->where('conf_billing_orders.is_archive', '=', '0');
    }

}
