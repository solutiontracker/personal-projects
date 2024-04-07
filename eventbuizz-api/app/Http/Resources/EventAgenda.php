<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Attendee as AttendeeResource;

class EventAgenda extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $agenda = [];
        foreach($this->resource as $req){
            $agenda[] = [
                'id' => $req->id,
                'event_id' => $req->event_id,
                'start_date' => $req->start_date,
                'start_time' => $req->start_time,
                'link_type' => $req->link_type,
                'workshop_id' => $req->workshop_id,
                'qa' => $req->qa,
                'ticket' => $req->ticket,
                'enable_checkin' => $req->enable_checkin,
                'enable_speakerlist' => $req->enable_speakerlist,
                'hide_on_registrationsite' => $req->hide_on_registrationsite,
                'info' => $this->when($req->info, new AgendaInfoCollection($req->info)),
                'workshop' => $this->when($req->program_workshop, new EventWorkshop($req->program_workshop)),
                'tracks' => $this->when($req->tracks && $req->tracks->count() > 0, EventTrack::collection($req->tracks)),
                'speakers' => $this->when($req->program_speakers && $req->program_speakers->count() > 0, AttendeeResource::collection($req->program_speakers)),
            ];
        }
        return $agenda;
    }
}
