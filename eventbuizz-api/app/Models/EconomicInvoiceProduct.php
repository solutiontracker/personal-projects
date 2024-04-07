<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicInvoiceProduct extends Model
{
    use SoftDeletes;
    protected $table = 'conf_economic_invoice_products';
    protected $fillable = ['bookedInvoiceNumber', 'lineNumber', 'sortKey', 'description', 'quantity', 'unitNetPrice', 'discountPercentage', 'unitCostPrice', 'vatRate', 'totalNetAmount', 'productNumber', 'unitNumber', 'departmentalDistributionNumber', 'productGroupNumber', 'is_credit', 'customerNumber', 'deliveryTerms', 'deliveryDate'];
    protected $dates = ['deleted_at'];
}
