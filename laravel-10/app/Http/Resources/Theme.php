<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Theme extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $theme = [];

        if($this->resource) {
            $theme = [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'thumbnail' => $this->thumbnail,
                'demo_url' => $this->demo_url,
                'is_paid' => $this->is_paid,
            ];
        }

        return $theme;
    }
}
