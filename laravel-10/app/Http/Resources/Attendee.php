<?php

namespace App\Http\Resources;

use App\Http\Resources\EventAttendee as EventAttendeeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Attendee extends JsonResource
{   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $attendee = [];
        if($this->resource) {
            $attendee = [
                'id'=> $this->id,
                'email' => $this->when($this->email && $this->gdpr, $this->email),
                'ss_number' => $this->when($this->ss_number && $this->gdpr, $this->ss_number),
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'organizer_id' => $this->when($this->organizer_id && $this->gdpr, $this->organizer_id),
                'FIRST_NAME_PASSPORT' => $this->when($this->FIRST_NAME_PASSPORT && $this->gdpr, $this->FIRST_NAME_PASSPORT),
                'LAST_NAME_PASSPORT' => $this->when($this->LAST_NAME_PASSPORT && $this->gdpr, $this->LAST_NAME_PASSPORT),
                'BIRTHDAY_YEAR' => $this->when($this->BIRTHDAY_YEAR && $this->gdpr, $this->BIRTHDAY_YEAR),
                'EMPLOYMENT_DATE' => $this->when($this->EMPLOYMENT_DATE && $this->gdpr, $this->EMPLOYMENT_DATE),
                'SPOKEN_LANGUAGE' => $this->when($this->SPOKEN_LANGUAGE && $this->gdpr, $this->SPOKEN_LANGUAGE),
                'image' => $this->when($this->image && $this->gdpr, $this->image),
                'status' => $this->when($this->status && $this->gdpr, $this->status),
                'show_home' => $this->when($this->show_home && $this->gdpr, $this->show_home),
                'allow_vote' => $this->when($this->allow_vote && $this->gdpr, $this->allow_vote),
                'phone' => $this->when($this->phone && $this->gdpr, $this->phone),
                'info' => $this->when($this->info && $this->gdpr, new AttendeeInfoCollection($this->info)),
                'event_attendee' => $this->when($this->currentEventAttendee && $this->gdpr, new EventAttendeeResource($this->currentEventAttendee)),
                'programs' => $this->when($this->programs, $this->programs),
                'labels' => $this->when($this->labels, $this->labels),
            ];
        }

        return $attendee;
    }
}
