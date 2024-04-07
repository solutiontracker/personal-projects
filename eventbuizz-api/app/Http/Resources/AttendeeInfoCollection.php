<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class attendeeInfoCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $info = [];

        foreach ($this->collection as $item) {
                if($item->value !== ""){
                    $info[$item->name] = $item->value;
                }
        }

        return $info;
    }
}
