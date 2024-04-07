<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteDocumentResultDocumentType extends Model
{
    use SoftDeletes;

    protected $table = 'conf_document_result_document_type';
    protected $fillable = ['id', 'document_result_id', 'document_type_id'];
    protected $dates = ['deleted_at'];
}

