<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventsiteBanner extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $banner = [];

        if($this->resource) {
            $banner = [
                'event_id' => $this->event_id,
                'banner_type' => $this->banner_type,
                'video_type' => $this->video_type,
                'video_duration' => $this->video_duration,
                'image' => '/assets/eventsite_banners/' . $this->image,
                'sort_order' => $this->sort_order,
                'status' => $this->status,
                'url' => $this->url,
                'title_color' => $this->title_color,
                'sub_title_color' => $this->sub_title_color,
            ];

            // append info fields
            if ($this->info->count() > 0) {
                foreach ($this->info as $item) {
                    $banner['info'][$item->name] = $item->value;
                }
            }
        }

        return $banner;
    }
}
