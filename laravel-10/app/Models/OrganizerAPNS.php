<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class OrganizerAPNS extends Model
    {
        use SoftDeletes;

        protected $table    = 'conf_organizer_apns';
        protected $fillable = ['id', 'organizer_id', 'key_id', 'team_id', 'apns_topic', 'private_key', 'jwt_token', 'issued_at', 'created_at', 'updated_at'];
        protected $dates    = ['deleted_at'];

    }
