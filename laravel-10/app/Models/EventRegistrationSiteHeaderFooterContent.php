<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegistrationSiteHeaderFooterContent extends Model
{
    use SoftDeletes;
    protected $table = 'conf_event_registration_site_header_footer_content';
    protected $fillable = ['event_id', 'organizer_name', 'country_id', 'address', 'zip'];
}