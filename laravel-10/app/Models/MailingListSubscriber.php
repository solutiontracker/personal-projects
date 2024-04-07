<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingListSubscriber extends Model
{
    use SoftDeletes;
    protected $table = 'conf_mailing_list_subscriber';
    protected $fillable = ['id', 'mailing_list_id', 'organizer_id', 'email', 'first_name', 'last_name', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];

}