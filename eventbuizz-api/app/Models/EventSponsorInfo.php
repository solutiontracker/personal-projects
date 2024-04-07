<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSponsorInfo extends Model
{
    protected $table = 'conf_sponsors_info';
    protected $fillable = ['id', 'value', 'name', 'sponsor_id', 'languages_id', 'status', 'created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function sponsors()
    {
        return $this->belongsTo('App\Models\EventSponsor', 'sponsor_id', 'id');
    }

}
