<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Map extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $map = [];

        if($this->resource) {

            $map = [
                'status' => $this->status,
                'google_map' => $this->google_map,
                'info' => $this->when(count($this->info) > 0, new MapInfoCollection($this->info))
            ];

        }
        return $map;
    }
}
