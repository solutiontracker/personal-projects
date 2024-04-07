<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TrackInfoCollection as TrackInfoResource;
class EventTrack extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $track = [];

        if($this->resource){
            $track = [
                'id' => $this->id,
                'parent_id' => $this->parent_id,
                'event_id' => $this->event_id,
                'status' => $this->status,
                'info' => $this->when($this->info, new TrackInfoCollection($this->info))
            ];
        }

        return $track;
    }
}
