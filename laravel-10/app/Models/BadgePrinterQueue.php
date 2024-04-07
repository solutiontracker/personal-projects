<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BadgePrinterQueue extends Model
{
    protected $table = 'conf_badges_printer_queue';
    protected $fillable = ['id', 'event_id', 'type', 'attendee_type',  'badge_id', 'name', 'firstname', 'lastname', 'companyName', 'title', 'companyAddress', 'privateAddress', 'telephone', 'mobile', 'mobile_2', 'mobile_3', 'mobile_4', 'mobile_5', 'mobile_6', 'mobile_7', 'mobile_8', 'mobile_9', 'mobile_10','interests','textfield','logo', 'image', 'bg_image', 'email', 'productArea', 'department', 'barcode', 'country', 'organization', 'delegateNumber', 'tableNumber' ,'networkGroup', 'created_at', 'printer', 'printer_group', 'printed', 'printed_at', 'name_1', 'firstname_1', 'lastname_1', 'companyName_1', 'title_1', 'barcode_1'];

    public $timestamps = false;
}
