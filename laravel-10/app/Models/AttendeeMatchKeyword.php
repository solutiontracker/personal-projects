<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendeeMatchKeyword extends Model {

	use SoftDeletes;
	protected $table = 'conf_attendee_match_keywords';
    protected $fillable = ['organizer_id','event_id','attendee_id','keyword_id','status'];
	protected $dates = ['deleted_at'];

	public function attendee()
    {
       return $this->belongsTo('\App\Models\Attendee','attendee_id','id');
    }

    public function keyword()
    {
        $this->belongsTo('\App\Models\MatchMaking', 'keyword_id', 'id');
    }
}