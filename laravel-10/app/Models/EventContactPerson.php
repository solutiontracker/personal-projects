<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventContactPerson extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_contact_person';
    protected $fillable = ['event_id', 'first_name', 'last_name', 'email', 'phone'];
}