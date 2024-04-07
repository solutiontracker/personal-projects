<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class News extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'title' => $this->title,
            'body' => $this->body,
            'image' => $this->image,
            'status' => $this->status,
            'scheduled_at' => \Carbon\Carbon::parse($this->scheduled_at)->format('D-M-Y H:i A'),
            'created_at' => \Carbon\Carbon::parse($this->created_at)->format('d F Y'),
        ];
    }
}
