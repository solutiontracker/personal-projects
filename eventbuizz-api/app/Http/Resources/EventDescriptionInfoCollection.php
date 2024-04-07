<?php


    namespace App\Http\Resources;

    use Illuminate\Http\Resources\Json\ResourceCollection;

    class EventDescriptionInfoCollection extends ResourceCollection
    {

        public function toArray($request)
        {
            $info = [];

            foreach ($this->collection as $item){
                $info[$item->name] = $item->value;
            }

            return $info;
        }
    }