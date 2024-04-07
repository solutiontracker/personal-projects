<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentInfo extends Model {

    protected $table = 'conf_payment_info';
    protected $fillable = ['name','value','payment_id','languages_id','status'];
}