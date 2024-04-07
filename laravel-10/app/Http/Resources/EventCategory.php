<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventCategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $categories = [];

        foreach ($this->resource as $key => $res) {
            
            $categories[$key] = [
                'id' => $res->id,
                'event_id' => $res->event_id,
                'color' => $res->color,
                'parent_id' => $res->parent_id,
                'cat_type' => $res->cat_type,
                'status' => $res->id,
            ];

            foreach ($res->info as $item) {
                     $categories[$key][$item['name']] = $item['value'];
            }
        }

        return $categories;
    }
}
