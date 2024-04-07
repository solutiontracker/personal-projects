<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class XmlLog extends Model
{
    use SoftDeletes;
    protected $table = 'conf_xml_log';
    protected $fillable = ['order_id', 'xml_send_date'];
    public $timestamps = false;
}
