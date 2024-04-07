<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackagePayment extends Model
{

    use SoftDeletes;
    protected $table = 'conf_package_payment';
    protected $fillable = ['admin_id', 'customer_agent_id', 'organizer_id', 'assign_package_id', 'invoice', 'amount',
        'invoice_date','sale_agent_id','contact_person','contact_person_email','contact_person_mobile','im_type',
        'im_id','first_contact_date','traning_session_date','description','currency'];
    protected $dates = ['deleted_at'];
}
