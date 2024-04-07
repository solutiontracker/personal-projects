<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class EventsiteDocumentResult extends Model
{
    use SoftDeletes;

    protected $table = 'conf_eventsite_document_results';
    protected $fillable = ['id', 'name', 'path', 'size', 'type', 'attendee_id', 'order_id'];
    protected $dates = ['deleted_at'];

    public function types()
    {
        return $this->belongsToMany('\App\Models\EventsiteDocumentType', 'conf_document_result_document_type', 'document_result_id', 'document_type_id');
    }
}

