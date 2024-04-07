<?php

namespace App\Http\Resources;

use App\Http\Resources\EventAttendee as EventAttendeeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Alert extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $news = [];

        if($this->resource) {
            $news = [
                'id' => $this->id,
                'event_id' => $this->event_id,
                'pre_schedule' => $this->pre_schedule,
                'alert_date' => $this->alert_date,
                'alert_time' => $this->alert_time,
                'sendto' => $this->sendto,
                'alert_email' => $this->alert_email,
                'alert_sms' => $this->alert_sms,
                'status' => $this->status,
                'display_alert_date' => \Carbon\Carbon::parse($this->alert_date)->format('d/m/y'),
                'info' => $this->when($this->info, new AlertInfoCollection($this->info)),
            ];
        }

        return $news;
    }
}
