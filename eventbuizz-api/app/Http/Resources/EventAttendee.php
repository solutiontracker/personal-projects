<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Resources\Json\JsonResource;

    class EventAttendee extends JsonResource
    {
        /**
         * Transform the resource into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            $event_info = [];

            if ($this->resource) {
                $event_info = [
                    'email_sent' => $this->email_sent,
                    'sms_sent' =>  $this->sms_sent,
                    'status' =>  $this->status,
                    'login_yet' =>  $this->login_yet,
                    'speaker' =>  $this->speaker,
                    'sponser' =>  $this->sponser,
                    'exhibitor' => $this->exhibitor,
                    'attendee_type' =>  $this->attendee_type ,
                    'default_language_id' =>  $this->default_language_id,
                    'is_active' =>  $this->is_active,
                    'gdpr' =>  $this->gdpr ,
                    'allow_vote' =>  $this->allow_vote ,
                    'allow_gallery' =>  $this-> allow_gallery ,
                    'ask_to_apeak' =>  $this->ask_to_apeak ,
                    'type_resource' =>  $this->type_resource ,
                    'accept_foods_allergies' =>  $this->accept_foods_allergies,
                    'allow_my_document' => $this->allow_my_document,
                ];
            }

            return $event_info;
        }
    }
