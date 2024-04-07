<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicInvoice extends Model
{
    use SoftDeletes;
    protected $table = 'conf_economic_invoices';
    protected $fillable = ['bookedInvoiceNumber', 'date', 'currency', 'exchangeRate', 'netAmount', 'netAmountInBaseCurrency', 'grossAmount', 'grossAmountInBaseCurrency', 'vatAmount', 'roundingAmount', 'remainder', 'remainderInBaseCurrency', 'dueDate', 'paymentTermsNumber', 'daysOfCredit', 'paymentTermsName', 'paymentTermsType', 'customerNumber', 'recipient_name', 'recipient_address', 'recipient_zip', 'recipient_city', 'recipient_country', 'recipient_ean', 'customerContactNumber', 'vatZoneNumber', 'layoutNumber', 'delivery_address', 'deliveryTerms', 'deliveryDate', 'type', 'is_credit', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];
}
