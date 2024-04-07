<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicCustomer extends Model
{
    use SoftDeletes;
    protected $table = 'conf_economic_customers';
    protected $fillable = ['customerNumber', 'email', 'currency', 'paymentTermsNumber', 'customerGroupNumber', 'address', 'balance', 'dueAmount', 'corporateIdentificationNumber', 'city', 'country', 'ean', 'name', 'zip', 'website', 'vatZoneNumber', 'layoutNumber', 'customerContactNumber', 'lastUpdated', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];
}
