<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class MatchMaking extends Model
{
    protected $table = 'conf_match_making';

    protected $fillable = ['event_id','organizer_id','parent_id','name','sort_order','status', 'registration_form_id'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function keywords()
    {
        return $this->hasMany('\App\Models\AttendeeMatchKeyword', 'keyword_id');
    }

    public function children()
    {
        return $this->hasMany('\App\Models\MatchMaking', 'parent_id');
    }
}
