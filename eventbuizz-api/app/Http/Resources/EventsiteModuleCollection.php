<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventsiteModuleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $modules = [];

        foreach($this->collection as $item){
            $modules[$item->alias] = $item->info[0]->value;
        }
        return $modules;
    }
}
