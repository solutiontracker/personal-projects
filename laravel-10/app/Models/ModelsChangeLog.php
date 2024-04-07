<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsChangeLog extends Model
{
    use HasFactory;
    protected $table = 'conf_models_change_logs';
    protected $fillable = ['organizer_id', 'event_id', 'module_alias',
        'action',
        'action_model',
        'model_id',
        'changed_column',
        'old_value',
        'new_value',
        'logged_by_id',
        'logged_by_user_type',
        'app_type'
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    /**
     * Set the model alias name.
     *
     * @param  string  $value
     * @return void
     */
    public function setActionModelAttribute($value)
    {
        $this->attributes['action_model'] = ($value);
        $this->attributes['module_alias'] = ($module);
    }
}
