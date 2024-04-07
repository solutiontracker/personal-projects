<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationFormTheme extends Model
{
    use SoftDeletes;

    protected $table = 'conf_registration_form_themes';

    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];
}