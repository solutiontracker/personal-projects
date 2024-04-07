<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventsiteVideo extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $videos = [];

        if($this->resource) {
            $videos = [
                'event_id' => $this->event_id,
                'thumnail' => $this->thumnail,
                'URL' => $this->URL,
                'type' => $this->type,
                'video_path' => $this->video_path,
                'status' => $this->status,
                'sort_order' => $this->sort_order
            ];

            foreach ($this->info as $item) {
                $videos['info'][$item->name] = $item->value;
            }
        }

        return $videos;
    }
}
