<?php
namespace App\Traits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelsChangeLog;
use Symfony\Component\HttpFoundation\Request;

trait Observable
{
    // bootObservable() will be called on model instantiation automatically
    public static function bootObservable() {
        static::created(function (Model $model) {
            static::logChange( $model, 'CREATED' );
        });
        static::updated(function (Model $model) {
            foreach ($model->getDirty() as $attribute=>$value){
                if($attribute != 'updated_at') {

                    if(str_contains(static::class, 'Info') && $attribute == 'value'){
                        $column = $model->getOriginal('name');
                    }else{
                        $column = $attribute;
                    }
                    $data = $value;
                    $original = $model->getOriginal($attribute);
                    if ($original != $data && $original != $data.':00' && $original != $data.' 00:00:00') {
                        static::logChange($model, 'UPDATED', $column, $data, $original);
                    }
                }
            }
        });

        static::deleted(function (Model $model) {
            static::logChange( $model, 'DELETED' );
        });
    }

    public static function logChange( Model $model, string $action , $column = null, $new_value = null, $original = null)
    {
        $data=getLogData();
        if(str_contains(static::class,'\\')){
            $action_model = explode('\\',static::class);
        }
        ModelsChangeLog::create([
            'organizer_id'=> $data['organizer_id'],
            'event_id'=> request()->get('event_id'),
            'app_type'=> $data['app_type'],
            'logged_by_id' => $data['logged_by_id'],
            'logged_by_user_type' =>  $data['logged_by_user_type'],
            'action_model' => is_array($action_model)?end($action_model):$action_model,
            'action'  => $action,
            'model_id'  => $model->id,
            'changed_column'  => $column,
            'old_value'  => $original,
            'new_value'  => $original==1&$new_value==''?0:$new_value,
        ]);
    }
    /**
     * String to describe the model being updated / deleted / created
     * Override this in the model class
     * @return string
     */
    public static function logSubject(Model $model): string {
        return static::logImplodeAssoc($model->attributesToArray());
    }
    public static function logImplodeAssoc(array $attrs): string {
        $l = '';
        foreach( $attrs as $k => $v ) {
            $l .= "{ $k => $v } ";
        }
        return $l;
    }
}
