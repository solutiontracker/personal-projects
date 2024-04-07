<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LeadTerm extends Model {

    protected $table = 'conf_leads_terms';
    use SoftDeletes;
    protected $fillable = ['event_id','term_text'];
    protected $dates = ['created_at'];
}