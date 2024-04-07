<?php


    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class EventSubregistrationMatrix extends Model
    {
        use SoftDeletes;
        protected $table = 'conf_event_sub_registration_matrix';
        protected $fillable = ['name', 'question_id','sort_order'];
        protected $dates = ['deleted_at'];
        protected $appends = ['value'];

        public function getValueAttribute()
        {
            return $this->attributes['name'];
        }
    }