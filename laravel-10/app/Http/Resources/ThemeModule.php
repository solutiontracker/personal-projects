<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThemeModule extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $module = [];

        if($this->resource) {
            $module = [
                'id' => $this->id,
                'alias' => $this->alias,
                'name' => $this->name,
                'variation_name' => $this->variation_name,
                'slug' => $this->slug,
                'status' => $this->status
            ];
        }

        return $module;
    }
}
