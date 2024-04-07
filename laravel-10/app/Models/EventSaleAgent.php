<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSaleAgent extends Model
{
    protected $table = 'conf_event_sale_agents';
    protected $fillable = ['id','sale_agent_id','event_id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
