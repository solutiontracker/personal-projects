<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class MyTurnListRepository extends AbstractRepository
{
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function getAttendeeDetail($formInput)
    {
        $attendee = \App\Models\EventAgendaTurnList::whereHas('attendee', function ($query) use ($formInput) {
            $query->where('id', $formInput['language_id']);
        })->with('attendee')
            ->where('agenda_id', $formInput['agenda_id'])
            ->orderBy('sort_order', 'ASC')
            ->first();

        if ($attendee) {
            return $attendee;
        } else {
            return \App\Models\Attendee::where('id', $formInput['attendee_id'])->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }])->first();
        }
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function getActiveSession($formInput)
    {
        return \App\Models\EventAgendaSpeakerlistSession::where('agenda_id', $formInput['agenda_id'])->orderBy('id', 'DESC')->first();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public static function getSetting($formInput)
    {
        return \App\Models\EventTurnListSetting::where('event_id', $formInput['event_id'])->first();
    }

    /**
     * @param mixed $formData
     *
     * @return [type]
     */
    public function sendChatMessage($formData)
    {
        return \App\Models\EventStreamingChannelChat::create([
            "event_id" => $formData['event_id'],
            "agenda_id" => $formData['agenda_id'],
            "attendee_id" => $formData['attendee_id'],
            "organizer_id" => $formData['organizer_id'],
            "ChannelName" => $formData['ChannelName'],
            "message" => $formData['message'],
            "sendBy" => $formData['sendBy'],
        ]);
    }

    /**
     * @param mixed $formData
     *
     * @return [type]
     */
    public function speakerRequest($formData)
    {
        return \App\Models\SpeakerRequest::where('event_id', $formData['event_id'])->where('agenda_id', $formData['agenda_id'])->where('attendee_id', $formData['attendee_id'])->first();
    }

    /**
     * @param mixed $formData
     *
     * @return [type]
     */
    public function getTurnListData($formData)
    {
        $query = \App\Models\EventAgendaTurnList::join('conf_event_attendees', function ($join) use ($formData) {
            $join->on('conf_event_attendees.attendee_id', '=', 'conf_event_agenda_turn_list.attendee_id')
                ->where('conf_event_attendees.event_id', $formData['event_id']);
        })
            ->where('conf_event_agenda_turn_list.agenda_id', $formData['agenda_id'])
            ->where('conf_event_agenda_turn_list.attendee_id', $formData['attendee_id'])
            ->where('conf_event_agenda_turn_list.status', $formData['status'])
            ->with(['attendee.info' => function ($q) use ($formData) {
                return $q->where('languages_id', $formData['language_id']);
            }])->select('conf_event_agenda_turn_list.*')->orderBy('sort_order');

        $final_attendees = array();

        if ($formData['status'] == 'inspeech') {
            return $attendee = $query->first();
        } else {
            $data = $query->get();
            foreach ($data as $attendee) {
                $temp_array = array();
                foreach ($attendee as $key => $value) {
                    if ($key == 'status') {
                        $temp_array['turn_status'] = $value;
                    } else if ($key == 'attendee') {
                        $attendee_array = readArrayKey($value, [], 'info');
                        $temp_array = array_merge($temp_array, $attendee_array[0]);
                    } else {
                        $temp_array[$key] = $value;
                    }
                }
                $final_attendees[] = $temp_array;
            }
            return $final_attendees;
        }
    }
}
