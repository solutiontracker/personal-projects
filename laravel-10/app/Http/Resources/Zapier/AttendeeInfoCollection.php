<?php

namespace App\Http\Resources\Zapier;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AttendeeInfoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $info = [];

        foreach($this->collection as $item){
            $info[$item->name] = $item->value;
        }

        return $info;
    }
}
