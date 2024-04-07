<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingList extends Model
{
    use SoftDeletes;
    protected $table = 'conf_mailing_list';
    protected $fillable = ['id', 'organizer_id', 'name', 'default_from_email','default_from_name','created_at','updated_at','deleted_at'];
    protected $dates = ['deleted_at'];

    public function info()
    {
        return $this->hasMany(MailingListInfo::class, 'mailing_list_id');
    }
}