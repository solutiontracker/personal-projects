<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventsitePhoto extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $photos = [];

        if($this->resource) {
            $photos = [
                'event_id' => $this->event_id,
                'image' => $this->image,
                'status' => $this->status,
                'sort_order' => $this->sort_order,
            ];

            foreach ($this->info as $item) {
                $photos['info'][$item->name] = $item->value;
            }
        }
        return $photos;
    }
}
