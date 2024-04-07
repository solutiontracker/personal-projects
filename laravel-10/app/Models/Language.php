<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model {
    protected $table = 'conf_languages';
    protected $fillable = ['name', 'lang_code', 'status','ios_lang_code'];
}