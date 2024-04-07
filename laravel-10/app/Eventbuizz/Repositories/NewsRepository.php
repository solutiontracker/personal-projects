<?php

namespace App\Eventbuizz\Repositories;

use \App\Models\EventNews;

class NewsRepository extends AbstractRepository
{
    protected $model;

    public function __construct(EventNews $model)
    {
        $this->model = $model;
    }

    public function getFrontNews($form_input, )
    {
        $event_id = $form_input['event']['id'];
        return $this->model::where('event_id', $event_id)->where(function($q){
            $q->where('status', 'publish')->orWhere(function($q) {
                $q->where('status', 'schedule')->where('scheduled_at', '<=', date('Y-m-d H:i:s'));
            });
        })->orderBy('scheduled_at','desc')->paginate($form_input['limit']);
    }

    public function getNewsDetail($form_input, $id)
    {
        return $this->model::where('event_id', $form_input['event']['id'])->where('id', $id)->where(function($q){
            $q->where('status', 'publish')->orWhere(function($q) {
                $q->where('status', 'schedule')->where('scheduled_at', '<=', date('Y-m-d H:i:s'));
            });
        })->first();
    }

}
