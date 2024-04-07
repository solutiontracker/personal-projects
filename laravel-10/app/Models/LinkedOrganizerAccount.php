<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkedOrganizerAccount extends Model
{
    use SoftDeletes;

    protected $table = 'conf_linked_organizer_accounts';
    protected $fillable = ['id', 'linked_parent_organizer_id', 'linked_organizer_id', 'organizer_id'];

   
}
