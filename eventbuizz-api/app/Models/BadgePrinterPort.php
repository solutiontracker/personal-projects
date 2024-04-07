<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BadgePrinterPort extends Model
{
    protected $table = 'conf_badges_printer_ports';
    protected $fillable = ['id', 'port', 'isAvailable', 'heart_beat'];

    public $timestamps = false;
}
