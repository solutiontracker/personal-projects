<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntegrationRule extends Model
{
    use SoftDeletes;

    protected $table = 'conf_integration_rules';
    protected $fillable = ['name', 'value', 'integration_id'];
    protected $dates = ['deleted_at'];

    public function integration()
    {
        return $this->belongsTo(IntegrationRule::class, 'integration_id');
    }

    public static function format($rules){
        $rules_arr = [];

        foreach ($rules as $item){
            $rules_arr[$item['name']] = $item['value'];
        }

        return $rules_arr;
    }
}
