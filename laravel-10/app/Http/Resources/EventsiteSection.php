<?php


    namespace App\Http\Resources;


    use Illuminate\Http\Resources\Json\JsonResource;

    class EventsiteSection extends JsonResource
    {
        public function toArray($request)
        {
            $sections = [];

            if($this->resource) {
                $sections = [
                    'event_id' => $this->event_id,
                    'alias' => $this->alias,
                    'is_purchased' => $this->is_purchased,
                    'status' => $this->status,
                    'icon' => $this->icon,
                    'sort_order' => $this->sort_order,
                ];

//                foreach ($this->info as $item) {
//                    $photos['info'][$item->name] = $item->value;
//                }
            }
            return $sections;
        }
    }