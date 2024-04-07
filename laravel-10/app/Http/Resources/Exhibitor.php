<?php

namespace App\Http\Resources;

use App\Http\Resources\EventCategory as EventCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Exhibitor extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $exhibitor = [];

        if($this->resource) {
            $exhibitor = [
                'id' => $this->id,
                'event_id' => $this->event_id,
                'name' => $this->when($this->name,$this->name),
                'email' => $this->when($this->email,$this->email),
                'logo' => $this->when($this->logo,$this->logo),
                'booth' => $this->when($this->booth,$this->booth),
                'phone_number' => $this->when($this->phone_number,$this->phone_number),
                'website' => $this->website,
                'twitter' => $this->twitter,
                'facebook' => $this->facebook,
                'linkedin' => $this->linkedin,
                'stype' => $this->stype,
                'allow_reservations' => $this->allow_reservations,
                'status' => $this->status,
                'allow_card_reader' => $this->allow_card_reader,
                'categories' => $this->when(($this->categories->count() > 0) && $this->show_cat, new EventCategoryResource($this->categories)),
            ];
        }

        return $exhibitor;
    }
}
