<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EventCategory as EventCategoryResource;

class EventSponsor extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sponsor = [];

        if($this->resource) {
            $sponsor = [
                'id' => $this->id,
                'event_id' => $this->event_id,
                'name' => $this->when($this->name,$this->name),
                'email' => $this->when($this->email,$this->email),
                'logo' => $this->when($this->logo,$this->logo),
                'booth' => $this->when($this->booth,$this->booth),
                'phone_number' => $this->when($this->phone_number, $this->phone_number),
                'website' => $this->when($this->website,$this->website),
                'twitter' => $this->when($this->twitter,$this->twitter),
                'facebook' =>$this->when($this->facebook,$this->facebook),
                'linkedin' => $this->when($this->linkedin,$this->linkedin),
                'stype' => $this->stype,
                'allow_reservations' => $this->allow_reservations,
                'status' => $this->status,
                'allow_card_reader' => $this->allow_card_reader,
                'categories' => $this->when(($this->categories->count() > 0) && $this->show_cat, new EventCategoryResource($this->categories)),
            ];
        }

        return $sponsor;
    }
}
