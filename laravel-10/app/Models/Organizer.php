<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Notifications\Auth\Organizer\ResetPasswordNotification;


class Organizer extends Authenticatable
{
    use Notifiable, HasApiTokens;
    use SoftDeletes;
    protected $guard = 'organizer';
    protected $table = 'conf_organizer';
    protected $fillable = ['first_name', 'last_name', 'parent_id', 'user_name', 'email', 'password', 'phone', 'address', 'house_number', 'company',
        'vat_number', 'zip_code', 'city', 'country', 'create_date', 'expire_date', 'domain', 'total_space',
        'space_private_document', 'sub_admin_limit', 'status', 'user_type', 'internal_organizer', 'legal_contact_first_name',
        'legal_contact_last_name', 'legal_contact_email', 'legal_contact_mobile', 'show_native_app_link_all_events',
        'allow_native_app','api_key','allow_api','allow_card_reader', 'authentication', 'authentication_type', 'license_start_date',
        'license_end_date', 'license_type', 'paid','email_marketing_template', 'mailing_list', 'language_id',
        'allow_admin_access', 'allow_plug_and_play_access', 'last_login_ip'];

    protected $hidden = [
        'password', 'remember_token',
    ];

	/**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany('Role');
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasRole($key)
    {
        foreach ($this->roles as $role) {
            if ($role->role_name === $key) {
                return true;
            }
        }
        return false;
    }

    public function events()
    {
        return $this->hasMany('Events','owner_id');
    }

    //Send password reset notification
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sub_admin_events()
    {
        return $this->hasMany(SubAdminEvent::class, 'admin_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'conf_assign_packages', 'organizer_id', 'package_id')
            ->withPivot(array('package_assign_date','id','no_of_event','organizer_id','package_expire_date'));
    }

    public function assignedPackages()
    {
        return $this->hasMany(AssignPackage::class, 'organizer_id', 'id');
    }
}
