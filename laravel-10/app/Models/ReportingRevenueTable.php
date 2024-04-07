<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportingRevenueTable extends Model
{
    use SoftDeletes;
    protected $table = 'conf_reporting_revenue_table';
    protected $fillable = ['event_id', 'order_ids','date','total_tickets','waiting_tickets','waiting_order_ids','total_revenue','event_total_tickets'];
    protected $dates = ['deleted_at'];
}