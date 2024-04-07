<?php

namespace App\Http\Resources\Zapier;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\Resource;

class Attendee extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $attendee = [];

        if($this->resource) {
            $attendee = [
                'id' => $this->log_id,
                'attendee_id' => $this->id,
                'email' => $this->email,
                'ss_number' => $this->ss_number,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'organizer_id' => $this->organizer_id,
                'FIRST_NAME_PASSPORT' => $this->FIRST_NAME_PASSPORT,
                'LAST_NAME_PASSPORT' => $this->LAST_NAME_PASSPORT,
                'BIRTHDAY_YEAR' => $this->BIRTHDAY_YEAR,
                'EMPLOYMENT_DATE' => $this->EMPLOYMENT_DATE,
                'SPOKEN_LANGUAGE' => $this->SPOKEN_LANGUAGE,
                'image' => $this->image,
                'status' => $this->status,
                'show_home' => $this->show_home,
                'allow_vote' => $this->allow_vote,
                'phone' => $this->phone,
                'info' => $this->when($this->info, new AttendeeInfoCollection($this->info)),
            ];
        }

        return $attendee;
    }
}
