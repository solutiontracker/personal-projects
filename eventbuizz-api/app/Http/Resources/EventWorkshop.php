<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\EventWorkshopInfoColllection as EventWorkshopInfoResource;

class EventWorkshop extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       $workshop = [];

       if($this->resource){
           $workshop = [
                'id' => $this->id,
               'event_id' => $this->event_id,
               'date' => $this->date,
               'start_time' => $this->start_time,
               'end_time' => $this->end_time,
               'info' => $this->when($this->info, EventWorkshopInfoResource::collection($this->info))
           ];
       }

       return $workshop;
    }
}
