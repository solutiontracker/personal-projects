<?php


    namespace App\Http\Resources;


    use Illuminate\Http\Resources\Json\JsonResource;

    class EventDescription extends JsonResource
    {

        /**
         * @param \Illuminate\Http\Request $request
         * @return array
         */

        public function toArray($request)
        {
            $description = [];

            if ($this->resource) {
                $description = [
                    'event_id' => $this->event_id,
                    'info' => $this->when($this->info, new EventDescriptionInfoCollection($this->info)),
                ];
            }

            return $description;
        }
    }