<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventSettingCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $settings = [];

        foreach ($this->collection as $item){
            if(isset($item->value) && $item->value !='') {
                $settings[$item->name] = $item->value;
            }
        }

        return $settings;
    }
}
