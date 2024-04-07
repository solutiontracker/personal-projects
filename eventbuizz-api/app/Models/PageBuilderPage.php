<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageBuilderPage extends Model
{
    use SoftDeletes;
    protected $table = 'conf_page_builder_pages';
    protected $fillable = ['id', 'event_id', 'status', 'name', 'assets', 'components', 'css', 'html', 'styles'];
    protected $dates = ['deleted_at'];
}
