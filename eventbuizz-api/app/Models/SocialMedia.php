<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMedia extends Model {

    use SoftDeletes;
    protected $table = 'conf_social_media';
    protected $fillable = ['event_id', 'name', 'value', 'select_type', 'sort_order'];
    protected $dates = ['deleted_at'];
}
