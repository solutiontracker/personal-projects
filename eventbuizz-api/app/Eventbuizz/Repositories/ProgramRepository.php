<?php

namespace App\Eventbuizz\Repositories;

use App\Models\EventAgenda;
use Illuminate\Http\Request;
use App\Eventbuizz\Repositories\EventRepository;
use Maatwebsite\Excel\Concerns\ToArray;

class ProgramRepository extends AbstractRepository
{
    public $infoFields = array('topic', 'description', 'date', 'start_time', 'end_time', 'location', 'tickets');

    public function __construct(Request $request, EventAgenda $model)
    {
        $this->formInput = $request;
        $this->model = $model;
    }

    /**
     * when new event create / cloning event
     *
     * @param array
     */
    public function install($request)
    {
        if ($request["content"]) {
            //Workshop
            $from_workshops = \App\Models\EventWorkshop::where("event_id", $request['from_event_id'])->get();
            if ($from_workshops) {
                foreach ($from_workshops as $from_workshop) {
                    $to_workshop = $from_workshop->replicate();
                    $to_workshop->event_id = $request['to_event_id'];
                    $to_workshop->save();

                    //workshop info
                    $from_workshop_info = \App\Models\EventWorkShopInfo::where("workshop_id", $from_workshop->id)->get();
                    foreach ($from_workshop_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->workshop_id = $to_workshop->id;
                        $info->save();
                    }

                    session()->put('clone.event.workshops.' . $from_workshop->id, $to_workshop->id);
                }
            }

            //Tracks
            $from_parent_tracks = \App\Models\EventTrack::where("event_id", $request['from_event_id'])->where("parent_id", 0)->get();
            if ($from_parent_tracks) {
                foreach ($from_parent_tracks as $from_parent_track) {
                    $to_parent_track = $from_parent_track->replicate();
                    $to_parent_track->event_id = $request['to_event_id'];
                    $to_parent_track->save();

                    //track info 
                    $from_parent_track_info = \App\Models\TrackInfo::where("track_id", $from_parent_track->id)->get();
                    foreach ($from_parent_track_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->track_id = $to_parent_track->id;
                        $info->save();
                    }

                    session()->put('clone.event.tracks.' . $from_parent_track->id, $to_parent_track->id);

                    //child tracks
                    $from_child_tracks = \App\Models\EventTrack::where("event_id", $request['from_event_id'])->where("parent_id", $from_parent_track->id)->get();
                    if ($from_child_tracks) {
                        foreach ($from_child_tracks as $from_child_track) {
                            $to_child_track = $from_child_track->replicate();
                            $to_child_track->parent_id = $to_parent_track->id;
                            $to_child_track->event_id = $request['to_event_id'];
                            $to_child_track->save();

                            //child track info 
                            $from_child_track_info = \App\Models\TrackInfo::where("track_id", $from_child_track->id)->get();
                            foreach ($from_child_track_info as $from_info) {
                                $info = $from_info->replicate();
                                $info->track_id = $to_child_track->id;
                                $info->save();
                            }

                            session()->put('clone.event.tracks.' . $from_child_track->id, $to_child_track->id);
                        }
                    }
                }
            }

            //programs
            $from_programs = \App\Models\EventAgenda::where('event_id', $request['from_event_id'])->get();
            if ($from_programs) {
                foreach ($from_programs as $from_program) {
                    $program = $from_program->replicate();
                    $program->event_id = $request['to_event_id'];

                    //workshop 
                    if ($program->workshop_id && session()->has('clone.event.workshops.' . $program->workshop_id)) {
                        $program->workshop_id = session()->get('clone.event.workshops.' . $program->workshop_id);
                    }

                    $program->save();

                    //Agenda Info
                    $from_program_info = \App\Models\AgendaInfo::where("agenda_id", $from_program->id)->get();
                    foreach ($from_program_info as $info) {
                        $info = $info->replicate();
                        $info->agenda_id = $program->id;
                        $info->save();
                    }

                    //Agenda Tracks
                    $from_agenda_tracks = \App\Models\EventAgendaTrack::where("agenda_id", $from_program->id)->get();
                    if ($from_agenda_tracks) {
                        foreach ($from_agenda_tracks as $from_agenda_track) {
                            if (session()->has('clone.event.tracks.' . $from_agenda_track->track_id)) {
                                $to_agenda_track = $from_agenda_track->replicate();
                                $to_agenda_track->agenda_id = $program->id;
                                $to_agenda_track->track_id = session()->get('clone.event.tracks.' . $from_agenda_track->track_id);
                                $to_agenda_track->save();
                            }
                        }
                    }

                    //Agenda videos
                    $from_agenda_videos = \App\Models\AgendaVideo::where("agenda_id", $from_program->id)->get();
                    foreach ($from_agenda_videos as $from_agenda_video) {
                        $to_agenda_video = $from_agenda_video->replicate();
                        $to_agenda_video->agenda_id = $program->id;
                        $to_agenda_video->save();
                    }

                    //Event Agenda Groups
                    $from_event_agenda_groups = \App\Models\EventAgendaGroup::where("agenda_id", $from_program->id)->get();
                    if ($from_event_agenda_groups) {
                        foreach ($from_event_agenda_groups as $from_event_agenda_group) {
                            if (session()->has('clone.event.event_groups.' . $from_event_agenda_group->group_id)) {
                                $to_event_agenda_group = $from_event_agenda_group->replicate();
                                $to_event_agenda_group->group_id = session()->get('clone.event.event_groups.' . $from_event_agenda_group->group_id);
                                $to_event_agenda_group->agenda_id = $program->id;
                                $to_event_agenda_group->save();
                            }
                        }
                    }

                    //save program to session for maintain back log
                    session()->put('clone.event.programs.' . $from_program->id, $program->id);
                }
            }
        }
    }

    /*
    * program import settings
    */
    static public function getImportSettings()
    {
        $settings = array(
            'fields' => array(
                'topic' => array(
                    'field' => 'topic',
                    'label' => 'Topic',
                    'type' => 'string',
                    'required' => true
                ),
                'description' => array(
                    'field' => 'description',
                    'label' => 'Description',
                    'type' => 'string',
                    'required' => false
                ),
                'date' => array(
                    'field' => 'date',
                    'label' => 'Date (dd-mm-yyyy)',
                    'type' => 'date',
                    'required' => true
                ),
                'start_time' => array(
                    'field' => 'start_time',
                    'label' => 'Start time (hh:mm)',
                    'type' => 'time',
                    'required' => true
                ),
                'end_time' => array(
                    'field' => 'end_time',
                    'label' => 'End time (hh:mm)',
                    'type' => 'time',
                    'required' => true
                ),
                'location' => array(
                    'field' => 'location',
                    'label' => 'Location',
                    'type' => 'string',
                    'required' => false
                ),

                'track_id' => array(
                    'field' => 'track_id',
                    'label' => 'Track id',
                    'type' => 'integer',
                    'required' => false
                ),
                'speaker_id' => array(
                    'field' => 'speaker_id',
                    'label' => 'Speaker id',
                    'type' => 'list',
                    'required' => false
                ),
                'group_id' => array(
                    'field' => 'group_id',
                    'label' => 'Group id',
                    'type' => 'list',
                    'required' => false
                ),
                'attendee_to_program' => array(
                    'field' => 'attendee_to_program',
                    'label' => 'Attendee to program',
                    'type' => 'list',
                    'required' => false
                ),
                'workshop_id' => array(
                    'field' => 'workshop_id',
                    'label' => 'Workshop id',
                    'required' => false
                ),

                'enable_checkin' => array(
                    'field' => 'enable_checkin',
                    'label' => 'Enable check-in',
                    'required' => false
                )
            )
        );

        return $settings;
    }

    /**
     * @param mixed $formInput
     * @param bool $pagination
     * 
     * @return [type]
     */
    public function listing($formInput, $pagination = true)
    {
        $heading_date = null;
        $result = \App\Models\EventAgenda::leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
            $join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
                ->where('a_end_time.name', '=', 'end_time')
                ->where('a_end_time.languages_id', '=', $formInput['language_id']);
        })
            ->where('conf_event_agendas.event_id', '=', $formInput['event_id'])
            ->with([
                'info' => function ($query) use ($formInput) {
                    $query->where('languages_id', '=', $formInput['language_id']);
                }, 'program_speakers',
                'program_workshop.info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput['language_id']);
                }, 'tracks.info' => function ($query) use ($formInput) {
                    return $query->where('languages_id', $formInput['language_id']);
                }, 'videos' => function ($query) {
                    return $query->where('status', 1);
                }
            ])
            ->whereHas('info', function ($q) use ($formInput) {
                if ($formInput['query']) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                }
            })
            ->whereNull('conf_event_agendas.deleted_at')
            ->orderBy('conf_event_agendas.start_date', 'ASC')
            ->orderBy('conf_event_agendas.start_time', 'ASC')
            ->orderBy('end_time', 'ASC')
            ->orderBy('conf_event_agendas.created_at', 'ASC')
            ->groupBy('conf_event_agendas.id')
            ->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'));

        if ($formInput['date']) {
            $result->whereDate('conf_event_agendas.start_date', $formInput['date']);
        }

        if ($formInput['hide_on_app']) {
            $result->where('conf_event_agendas.hide_on_app', 1);
        }

        if ($formInput['only_for_qa']) {
            $result->where('conf_event_agendas.only_for_qa', 1);
        }

        if ($pagination) {
            $programs = $result->paginate($formInput['limit'])->toArray();
        } else {
            $programs = $result->get()->toArray();
        }

        foreach ($programs['data'] as $key => $row) {
            $rowData = array();
            $infoData = readArrayKey($row, $rowData, 'info');
            $rowData['id'] = $row['id'];
            $rowData['topic'] = isset($infoData['topic']) ? $infoData['topic'] : '';
            $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
            $rowData['date'] = isset($infoData['date']) ? date('Y-m-d', strtotime($infoData['date'])) : '';

            if ($formInput['interface_language_id'] == 1) {
                $rowData['heading_date'] = ($heading_date != $rowData['date'] ? \Carbon\Carbon::parse($rowData['date'])->format('l, F jS,  Y') : '');
            } else {
                $rowData['heading_date'] = ($heading_date != $rowData['date'] ? set_lang(\Carbon\Carbon::parse($rowData['date'])->format('l j. F Y')) : '');
            }

            $rowData['start_time'] = isset($infoData['start_time']) ? $infoData['start_time'] : '';
            $rowData['end_time'] = isset($infoData['end_time']) ? $infoData['end_time'] : '';
            $rowData['location'] = isset($infoData['location']) ? $infoData['location'] : '';

            //program speakers
            $program_speakers = array();
            if (count($row['program_speakers'] ?? []) > 0) {

                $k = 0;
                foreach ($row['program_speakers'] as $speaker_detail) {
                    $program_speakers[] = $speaker_detail['first_name'] . ' ' . $speaker_detail['last_name'];
                    $k++;
                }
            }
            $rowData['program_speakers'] = $program_speakers;

            //program workshops
            $workshop_programs = array();
            if (count($row['program_workshop']['info'] ?? []) > 0) {
                foreach ($row['program_workshop']['info'] as $val) {
                    $workshop_programs[$val['name']] = $val['value'];
                }
            }
            $rowData['workshop_programs'] = $workshop_programs;

            //program tracks
            $program_tracks = array();
            if (count($row['tracks'] ?? []) > 0) {
                $temp_tracks = array();
                $k = 0;
                foreach ($row['tracks'] as $index => $tracks) {
                    $temp_tracks[] = $tracks['info'][0]['value'];
                    $k++;
                }
                $program_tracks = $temp_tracks;
            }
            $rowData['program_tracks'] = $program_tracks;

            //program videos
            $rowData['program_videos'] = $row['videos'];

            $programs['data'][$key] = $rowData;
            $heading_date = $rowData['date'];
        }
        return $programs;
    }

    public function setCustomFormInput($formInput)
    {
        $formInput['date'] = date('Y-m-d', strtotime($formInput['date']));
        $formInput['start_date'] = date('Y-m-d', strtotime($formInput['date']));
        $formInput['start_time'] = $formInput['start_time'] ? \Carbon\Carbon::parse($formInput['start_time'])->toTimeString() : '';
        $formInput['end_time'] = $formInput['end_time'] ? \Carbon\Carbon::parse($formInput['end_time'])->toTimeString() : '';
        $this->setFormInput($formInput);
        return $this;
    }

    public function createProgram($formInput)
    {
        $formInput = get_trim_all_data($formInput);
        $this->setCustomFormInput($formInput)
            ->create()
            ->insertInfoData();
        EventRepository::add_module_progress($formInput, "program");
    }

    public function insertInfoData()
    {
        $formInput = $this->getFormInput();
        $languages = get_event_languages($formInput['event_id']);
        $info = array();
        foreach ($languages as $key) {
            foreach ($this->infoFields as $field) {
                if (isset($formInput[$field])) {
                    if ($field == 'start_time' || $field == 'end_time') {
                        $info[] = new \App\Models\AgendaInfo(array('name' => $field, 'value' => $formInput[$field], 'languages_id' => $key, 'status' => 1));
                    } else {
                        $info[] = new \App\Models\AgendaInfo(array('name' => $field, 'value' => trim($formInput[$field]), 'languages_id' => $key, 'status' => 1));
                    }
                }
            }
        }

        $object = $this->getObject();
        $object->info()->saveMany($info);
        return $this;
    }

    public function updateProgram($formInput, $object)
    {
        $formInput = get_trim_all_data($formInput);
        $this->setCustomFormInput($formInput)
            ->update($object)
            ->updateInfoData($object->id);
    }

    public function updateInfoData($id)
    {
        $formInput = $this->getFormInput();
        $languages = get_event_languages($formInput['event_id']);
        foreach ($languages as $key) {
            foreach ($this->infoFields as $field) {
                if (isset($formInput[$field])) {
                    $info = \App\Models\AgendaInfo::where('agenda_id', '=', $id)->where('languages_id', '=', $key)->where('name', '=', $field)->first();
                    if (!$info) {
                        \App\Models\AgendaInfo::create([
                            "name" => $field,
                            "agenda_id" => $id,
                            "languages_id" => $key,
                            "status" => 1,
                            "value" => ($field == 'start_time' || $field == 'end_time') ? $formInput[$field] : trim($formInput[$field])
                        ]);
                    } else {
                        $info->value = ($field == 'start_time' || $field == 'end_time') ? $formInput[$field] : trim($formInput[$field]);
                        $info->save();
                    }
                }
            }
        }

        return $this;
    }

    public function deleteProgram($formInput, $id)
    {
        $event_id = $formInput['event_id'];
        $program = \App\Models\EventAgenda::find($id);
        $program->delete();

        $programInfo = \App\Models\AgendaInfo::where('agenda_id', $id);
        $programInfo->delete();

        $trunList = \App\Models\EventAgendaTurnList::where('agenda_id', $id);
        $trunList->delete();

        $qa = \App\Models\QA::where('agenda_id', $id);
        $qa->delete();

        $poll = \App\Models\EventPoll::where('agenda_id', '=', $id);
        $poll->delete();

        $agendaTrack =   \App\Models\EventAgendaTrack::where('agenda_id', '=', $id);
        $agendaTrack->delete();

        $directory =   \App\Models\Directory::where('agenda_id', '=', $id);
        $directory->delete();

        $speakerListLiveLog =   \App\Models\EventSpeakerlistLiveLog::where('agenda_id', '=', $id);
        $speakerListLiveLog->delete();

        $deleteGroups = \DB::update(\DB::raw("UPDATE conf_event_agenda_groups SET deleted_at = '" . date('Y-m-d H:i:s') . "',updated_at = '" . date('Y-m-d H:i:s') . "'  WHERE agenda_id='" . $id . "'"));
        $agendaNotes = \DB::update(\DB::raw("UPDATE conf_agenda_notes SET deleted_at = '" . date('Y-m-d H:i:s') . "' WHERE event_id ='" . $event_id . "' AND agenda_id ='" . $id . "' "));

        $programAttach = \App\Models\EventAgendaAttendeeAttached::where('agenda_id', '=', $id);
        $programAttach->delete();

        $programSpeaker = \App\Models\EventAgendaSpeaker::where('event_id', '=', $event_id)->where('agenda_id', $id);
        $speakers = $programSpeaker->get();
        foreach ($speakers as $speaker) {
            \App\Models\Directory::where('event_id', '=', $event_id)->where('speaker_id', '=', $speaker->attendee_id)->delete();
            $count = \App\Models\EventAgendaSpeaker::where('event_id', '=',$event_id)->where('attendee_id', $speaker->attendee_id)->count();
            if ($count == 0) {
                \DB::update(\DB::raw("UPDATE conf_event_attendees SET speaker= '0',updated_at = '" . date('Y-m-d H:i:s') . "' WHERE event_id ='" . $event_id . "' AND attendee_id ='" . $speaker->attendee_id . "' "));
            }
        }
        $programSpeaker->delete();

        return $this;
    }

    /**
     * validate import content for program
     * @param array
     * @param array
     * @param array
     * @param string
     */
    static public function import($formInput, $mapping, $data, $import_type = "new")
    {
        $result = array();
        $result['new'] = array();
        $result['duplicate'] = array();
        $result['error'] = array();
        $settings = self::getImportSettings();
        $event_id = $formInput['event_id'];
        $language_id = $formInput['language_id'];
        $languages = get_event_languages($formInput['event_id']);
        if ($import_type == 'new') {
            foreach ($data as $key => $values) {
                $record = array();
                $error = "";
                $info_fields = array();
                $valid = true;
                $prg_tracks = $prg_speakers = $prg_groups = $prg_attendees  = '';
                foreach ($values as $index => $val) {
                    if (isset($mapping[$index]) && $mapping[$index]) {
                        $formInput['event_id'] = $formInput['event_id'];
                        if ($mapping[$index] == 'topic') {
                            $info_fields[] = array('name' => 'topic', 'value' => trim($val));
                        } elseif ($mapping[$index] == 'description') {
                            $info_fields[] = array('name' => 'description', 'value' => trim($val));
                        } elseif ($mapping[$index] == 'start_time') {
                            if (verifyTime($val)) {
                                $error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
                                $valid = false;
                                break;
                            }
                            $info_fields[] = array('name' => 'start_time', 'value' => \Carbon\Carbon::parse(trim($val))->toTimeString());
                        } elseif ($mapping[$index] == 'end_time') {
                            if (verifyTime($val)) {
                                $error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
                                $valid = false;
                                break;
                            }
                            $info_fields[] = array('name' => 'end_time', 'value' => \Carbon\Carbon::parse(trim($val))->toTimeString());
                        } elseif ($mapping[$index] == 'location') {
                            $info_fields[] = array('name' => 'location', 'value' => trim($val));
                        } elseif ($mapping[$index] == 'tickets') {
                            $info_fields[] = array('name' => 'tickets', 'value' => trim($val));
                        } elseif ($mapping[$index] == 'date') {
                            if (verifyDate($val) != '1') {
                                $error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
                                $valid = false;
                                break;
                            }
                            $info_fields[] = array('name' => 'date', 'value' => \Carbon\Carbon::parse(trim($val))->toDateString());
                        } elseif ($mapping[$index] == 'track_id') {
                            $prg_tracks = $val;
                        } elseif ($mapping[$index] == 'speaker_id') {
                            $prg_speakers = $val;
                        } elseif ($mapping[$index] == 'group_id') {
                            $prg_groups = $val;
                        } elseif ($mapping[$index] == 'attendee_to_program') {
                            $prg_attendees = $val;
                        }
                        //VALIDATE DATA
                        if (trim($prg_tracks)) {
                            $track_ids = explode(";", trim($prg_tracks));
                            foreach ($track_ids as $track_id) {
                                $count = \App\Models\EventTrack::where('id', $track_id)->where('event_id', $formInput['event_id'])->count();
                                if ($count < 1) {
                                    $error = sprintf(__('messages.import_track_not_found'), $mapping[$index], $val);
                                    $valid = false;
                                    break;
                                }
                            }
                        }
                        if (trim($prg_speakers)) {
                            $speaker_ids = explode(";", trim($prg_speakers));
                            foreach ($speaker_ids as $speaker_id) {
                                $count = \App\Models\EventAttendee::where('attendee_id', $speaker_id)->where('event_id', $formInput['event_id'])->count();
                                if ($count < 1) {
                                    $error = sprintf(__('messages.import_speaker_not_found'), $mapping[$index], $val);
                                    $valid = false;
                                    break;
                                }
                            }
                        }

                        if (trim($prg_groups)) {
                            $group_ids = explode(";", trim($prg_groups));
                            foreach ($group_ids as $group_id) {
                                $count = \App\Models\EventGroup::where('id', $group_id)->where('event_id', $formInput['event_id'])->count();
                                if ($count < 1) {
                                    $error = sprintf(__('messages.import_group_not_found'), $mapping[$index], $val);
                                    $valid = false;
                                    break;
                                }
                            }
                        }
                        if (trim($prg_attendees)) {
                            $attendee_ids = explode(";", trim($prg_attendees));
                            foreach ($attendee_ids as $attendee_id) {
                                $count = \App\Models\EventAttendee::where('attendee_id', $attendee_id)->where('event_id', $formInput['event_id'])->count();
                                if ($count < 1) {
                                    $error = sprintf(__('messages.import_field_not_found'), $mapping[$index], $val);
                                    $valid = false;
                                    break;
                                }
                            }
                        }

                        if ($mapping[$index] == 'workshop_id' && trim($val) != '') {
                            $count = \App\Models\EventWorkshop::where('id', $val)->where('event_id', $formInput['event_id'])->count();
                            if ($count < 1) {
                                $error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
                                $valid = false;
                                break;
                            }
                        }

                        if (isset($settings['fields'][$mapping[$index]]['required']) && $settings['fields'][$mapping[$index]]['required'] == 1 && trim($val) == '') {
                            $error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
                            $valid = false;
                            break;
                        }

                        if ($mapping[$index] == 'id') {
                            $count = \App\Models\EventAgenda::where('id', $val)->count();
                            if ($count > 0) {
                                $error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
                                $result['duplicate'][] = $values;
                                $valid = false;
                                break;
                            }
                        }

                        if ($mapping[$index] == '-1') { //Do not map this field
                            $val   = '';
                        }



                        if ($mapping[$index] == 'enable_checkin') {
                            if ($val) {
                                $record[$mapping[$index]] = $val;
                            } else {
                                $record[$mapping[$index]] = 0;
                            }
                        } else {
                            $record[$mapping[$index]] = trim($val);
                        }
                    }
                }

                if ($valid) {
                    //store attendee
                    $record['event_id'] = $event_id;
                    $record['language_id'] = $language_id;
                    $record['language_id'] = '1';
                    $record['start_date'] = \Carbon\Carbon::parse($record['date'])->toDateString();
                    $record['start_time'] = \Carbon\Carbon::parse($record['start_time'])->toTimeString();
                    $record['end_time'] = \Carbon\Carbon::parse($record['end_time'])->toTimeString();

                    //program creation
                    $program = \App\Models\Agenda::create($record);

                    foreach ($languages as $language_id) {
                        foreach ($info_fields as $info) {
                            $formInput['agenda_id'] = $program->id;
                            $formInput['languages_id'] = $language_id;
                            $formInput['name'] = $info['name'];
                            if ($info['name'] == 'date') {
                                $formInput['value'] = date("Y-m-d", strtotime($info['value']));
                            } else {
                                $formInput['value'] = $info['value'];
                            }
                            if ($info['name'] == 'start_time') {
                                $formInput['value'] = date('H:i:s', strtotime($info['value']));
                            }
                            if ($info['name'] == 'end_time') {
                                $formInput['value'] = date('H:i:s', strtotime($info['value']));
                            }
                            \App\Models\AgendaInfo::create($formInput);
                        }
                    }

                    //track creation
                    if (trim($prg_tracks)) {
                        $track_ids = explode(";", trim($prg_tracks));
                        foreach ($track_ids as $track_id) {
                            $program->tracks()->attach($track_id);
                        }
                    }

                    //Speaker
                    if (trim($prg_speakers)) {
                        $event_agenda_speakers = array();
                        $speaker_ids = explode(";", trim($prg_speakers));
                        foreach ($speaker_ids as $speaker_id) {
                            $event_agenda_speakers[] = new \App\Models\EventAgendaSpeaker(array('event_id' => $event_id, 'attendee_id' => $speaker_id));
                            $program->attendee_assign()->saveMany($event_agenda_speakers);
                            \App\Models\EventAttendee::where('event_id', $event_id)->where('attendee_id', $speaker_id)->update([
                                "speaker" => '1'
                            ]);
                        }
                    }

                    //Groups
                    if (trim($prg_groups)) {
                        $groups_ids = explode(";", trim($prg_groups));
                        foreach ($groups_ids as $group_id) {
                            $program->groups()->attach($group_id);
                        }
                    }

                    //Attach Attendee
                    if (trim($prg_attendees)) {
                        $attendee_ids = explode(";", trim($prg_attendees));
                        foreach ($attendee_ids as $attendee_id) {
                            $info_saved = array('agenda_id' =>  $program->id, 'attendee_id' => $attendee_id, 'added_by' => 1);
                            \App\Models\EventAgendaAttendeeAttached::create($info_saved);
                        }
                    }

                    $result['new'][] = $record;
                } else {
					$record["error"] = $error;
					$result['error'][] = $record;
				}
            }
        }
        return $result;
    }

    /**
     * Event programs
     * @param array
     */
    static public function getAllPrograms($formInput)
    {
        $programs =  \App\Models\EventAgenda::leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
            $join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
                ->where('a_end_time.name', '=', 'end_time')
                ->where('a_end_time.languages_id', '=', $formInput['language_id']);
        })
            ->where('conf_event_agendas.event_id', '=', $formInput['event_id'])
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', '=', $formInput['language_id']);
            }])
            ->orderBy('conf_event_agendas.start_date', 'ASC')
            ->orderBy('conf_event_agendas.start_time', 'ASC')
            ->orderBy('end_time', 'ASC')
            ->orderBy('conf_event_agendas.created_at', 'ASC')
            ->groupBy('conf_event_agendas.id')
            ->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'))
            ->get()
            ->toArray();
        $returned_programs = [];
        foreach ($programs as $program) {
            $id = $program['id'];
            foreach ($program['info'] as $info) {
                if ($info['name'] == 'topic') {
                    $name = $info['value'];
                    break;
                }
            }
            $returned_programs[] = ['id' => $id, 'name' => date('d F Y', strtotime($program['start_date'])) . ' - ' . date('H:i', strtotime($program['start_time'])) . ' - ' . $name];
        }
        return $returned_programs;
    }

    /**
     * Event workshops
     * @param array
     */
    static public function getAllWorkshops($formInput)
    {
        $label = eventsite_labels("agendas", $formInput, 'SELECT_WORKSHOP_HEADLINE_NEW');
        $workshop_array = array();
        $workshops = \App\Models\EventWorkshop::where('event_id', $formInput['event_id'])->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput['language_id']);
        }])->get(['id'])->toArray();

        $workshop_array[0] = $label;
        foreach ($workshops as $workshop) {
            $id = $workshop['id'];
            foreach ($workshop['info'] as $info) {
                if ($info['name'] == 'name') {
                    $name = $info['value'];
                    break;
                }
            }
            $workshop_array[$id] = $name;
        }
        return $workshop_array;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    static public function getSetting($formInput)
    {
        return \App\Models\AgendaSetting::where('event_id', $formInput['event_id'])->first();
    }

    /**
    * @param mixed $formInput
    * @return [type]
    */
    static public function getProgram($formInput)
    {
        $container = array();

        $query = \App\Models\EventAgenda::leftJoin('conf_agenda_video', function ($join) use ($formInput) {
            $join->on('conf_agenda_video.agenda_id', '=', 'conf_event_agendas.id');
        });

        $query->where('conf_event_agendas.event_id', $formInput['event_id']);

        if(isset($formInput['program_id'])) {
            $query->where('conf_event_agendas.id', $formInput['program_id']);
        }

        $program = $query->select(array('conf_event_agendas.*'))->first();

        $program_array = \App\Models\EventAgenda::find($program->id);

        if($program_array) {

            $information = $program_array->info()->where('languages_id', $formInput['language_id'])->get();

            $videos = $program_array->videos()->get();

            $tracks = $program_array->tracks()->where('agenda_id', '=', $program->id)->get();

            $speakers = $program_array->program_speakers()->where('agenda_id', '=', $program->id)->get();

            $groups = $program_array->groups()->where('agenda_id', '=', $program->id)->get();

            $fields = array();

            foreach ($information as $info) {
                $fields[$info->name] = $info->value;
                if ($info->name == 'date') {
                    $fields[$info->name] = date('d-m-Y', strtotime($info->value));
                }
            }

            //workshop
            $fields['workshop_id'] = $program_array->workshop_id;

            $container = $program_array->toArray();

            $container['info'] = $fields;

            $container['tracks'] = $tracks;

            $container['speakers'] = $speakers;

            $container['groups'] = $groups;

            $container['info']['videos'] = $videos;
        }

        return $container;
    }

    /**
    * @param mixed $formInput
    * @return [type]
    */
    public static function getProgramInfo($formInput, $field = '')
    {
        $agenda = \App\Models\EventAgenda::where('id', $formInput['agenda_id'])->first();

        if($agenda) {

            $information = $agenda->info()->where('languages_id', $formInput['language_id'])->get();

            $fields = array();

            foreach ($information as $info) {
                $fields[$info->name] = $info->value;
            }

            $agenda = $agenda->toArray();

            $agenda['info'] = $fields;
        }

        if($field == "schedule") {
            return \Carbon\Carbon::parse($agenda['date'])->toDateString() . " ".\Carbon\Carbon::parse($agenda['info']['start_time'])->toTimeString(). "-".\Carbon\Carbon::parse($agenda['info']['end_time'])->toTimeString();
        } else {
            return $agenda;
        }
    }

    /**
    * @param mixed $formInput
    * 
    * @return [type]
    */
    public static function videos($formInput)
    {
        $programs = \App\Models\EventAgenda::leftJoin('conf_agenda_video', 'conf_agenda_video.agenda_id', '=', 'conf_event_agendas.id')
        ->leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
            $join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
            ->where('a_end_time.name', '=', 'end_time')
            ->where('a_end_time.languages_id', $formInput['language_id']);
        })
        ->where('conf_event_agendas.event_id', $formInput['event_id'])
        ->has('videos', '>', 0)
        ->with([
            'info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }, 'videos'
        ])
        ->whereNull('conf_event_agendas.deleted_at')
        ->orderBy('conf_event_agendas.start_date', 'ASC')
        ->orderBy('conf_event_agendas.start_time', 'ASC')
        ->orderBy('end_time', 'ASC')
        ->orderBy('conf_event_agendas.created_at', 'ASC')
        ->groupBy('conf_event_agendas.id')
        ->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'))
        ->get()->toArray();

        foreach ($programs as $i => $row) {
            
            $row['heading_date'] = getFormatDate('%A %e. %B %Y', $row['start_date']);
            $row['start_time'] = \Carbon\Carbon::parse($row['start_time'])->format('H:i');
            $row['end_time'] = \Carbon\Carbon::parse($row['end_time'])->format('H:i');
            
            $temp = array();
            if (count($row['info'] ?? []) > 0) {
                foreach ($row['info'] as $val) {
                    $temp[$val['name']] = $val['value'];
                }
            }
          
            $row['prg_detail'] = $temp;

            if(isset($formInput['id']) && $formInput['id'] == $row['id']) {
                $active_program = $row;
                unset($programs[$i]);
                continue;
            }

            $programs[$i] = $row;

        }

        if(isset($active_program) && $active_program) array_unshift($programs,$active_program);
        return $programs;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function schedules($formInput)
    {
        $dates = array();
        $programs =  \App\Models\EventAgenda::where('event_id', $formInput['event_id'])
            ->groupBy('start_date')
            ->orderBy('start_date', 'ASC')
            ->select('start_date')
            ->get();
        foreach($programs as $key => $program) {
            $dates[$key] = $program->start_date;
        }
        return $dates;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function attachedAttendees($formInput, $count = false)
    {
        $query = \App\Models\EventAgendaAttendeeAttached::where('agenda_id', $formInput['program_id']);
        if(isset($formInput['attendee_id'])) {
            $query->where('attendee_id', $formInput['attendee_id']);
        }
        if($count) {
            return $query->count();
        } else {
            return $query->get();
        }
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public static function tracks($formInput)
    {
        $tracks = array();
        $query = \App\Models\EventTrack::join('conf_event_agenda_tracks', 'conf_event_agenda_tracks.track_id', '=', 'conf_event_tracks.id')->where('event_id', $formInput["event_id"])->where('parent_id', '0')
            ->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }]);

        $parents = $query->select("conf_event_tracks.*")->orderBy('sort_order', 'asc')->groupBy('conf_event_tracks.id')->get();

        foreach ($parents as $parent) {
            $id = $parent['id'];
            foreach ($parent->info as $info) {
                $name = $info['value'];
                break;
            }
            $tracks[] = array("id" => $id, "name" => $name, "parent" => true);
            $sub_query = \App\Models\EventTrack::join('conf_event_agenda_tracks', 'conf_event_agenda_tracks.track_id', '=', 'conf_event_tracks.id')->where('event_id', $formInput["event_id"])->where('parent_id', $id)->with(['info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput["language_id"]);
            }]);

            $childs = $sub_query->select("conf_event_tracks.*")->orderBy('sort_order', 'asc')->groupBy('conf_event_tracks.id')->get();

            foreach ($childs as $child) {
                $child_id = $child['id'];
                foreach ($child['info'] as $child_info) {
                    $name = $child_info['value'];
                    break;
                }
                $tracks[] = array("id" => $child_id, "name" => $name, "parent" => false);
            }
        }

        return $tracks;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    static public function getProgramVideo($formInput)
    {
        return \App\Models\AgendaVideo::where('agenda_id', $formInput['agenda_id'])->where('id', $formInput['id'])->first();
    }

    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function getAllParentGroups($formInput)
    {
        $groups = \App\Models\EventGroup::where('event_id', $formInput['event_id'])
            ->where('parent_id', '=', '0')
            ->with(['info' => function ($query) use($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }])
            ->with(['children' => function ($r) {
                return $r->orderBy('sort_order');
            }, 'children.childrenInfo' => function ($query) use($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }, 'children.assignAgendaGroups'])
			->orderBy('sort_order')->get();
			
        return $groups;
    }
    
    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function getAgendaGroupsIds($formInput)
    {
        $agendaGroups = \App\Models\EventAgendaGroup::where('agenda_id', $formInput['program_id'])->get();
        $groups = $this->getAllParentGroups($formInput);
        $groups_array = array();
        foreach ($groups as $group) {
            if(count($group['children'])>0) {
                foreach ($group['children'] as $mod) {
                    foreach ($agendaGroups as $agendaGroup) {
                        if ($mod['id'] == $agendaGroup['group_id']) {
                            array_push($groups_array, $agendaGroup['group_id']);
                        }
                    }
                }
            }
        }
        return $groups_array;
    }

    public function getFrontPrograms($formInput){
        $lang_id = $formInput['language_id'];
        $programs = EventAgenda::ofEvent($formInput['event_id'])->with([
            'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },
            'tracks.info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); } ,
            'program_workshop.info'  => function($q) use($lang_id) { $q->where('languages_id', $lang_id); } ,
            'program_speakers.info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); }
            ])->get();
        return $programs;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    static public function saveVonageSession($formInput)
    {
        $video = \App\Models\AgendaVideo::where('id', $formInput['video_id'])->first();

        $video->sessionId = $formInput['sessionId'];

        $video->save();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    static public function saveProgramVonageSession($formInput)
    {
        $event_agenda = \App\Models\EventAgenda::where('id', $formInput['agenda_id'])->where('event_id', $formInput['event_id'])->first();

        $event_agenda->vonageSessionId = $formInput['vonageSessionId'];

        $event_agenda->save();
    }

    public static function getProgramTicket($formInput)
    {
        $program = \App\Models\EventAgenda::where('id', $formInput['id'])->first();
        
        if($program->ticket > 0 ) {

            if(isset($formInput['draft_orders']) && !empty($formInput['draft_orders'])) {

                $attendee_ids = (array)\App\Models\EventOrderSubRegistrationAnswer::whereIn('order_id', $formInput['draft_orders'])->where('agenda_id', $formInput['id'])->pluck('attendee_id')->toArray();

                $count = \App\Models\EventAgendaAttendeeAttached::where('agenda_id', $formInput['id'])->whereNotIn('attendee_id', array_unique($attendee_ids))->count();

                $draft_count = (int)count($attendee_ids);

                $ticket_left = $program->ticket - ($count + $draft_count);
                
            } else {

                $count = \App\Models\EventAgendaAttendeeAttached::where('agenda_id', $formInput['id'])->count();

                $ticket_left = $program->ticket - $count;
            }

        } else {
            $ticket_left = 'unlimited';
        }

        return $ticket_left;
    }

    /**
     * Workshops
     * @param array
     */
    static public function workshops($formInput)
    {
        $query = \App\Models\EventWorkshop::where('event_id', '=', $formInput['event_id']);

        $query->with([
            'info' => function ($query) use ($formInput) {
                $query->where('languages_id', '=', $formInput['language_id']);
            }
        ]);
        
        //Workshop
        if ($formInput['workshop_id']) {
            $query->where('id', $formInput['workshop_id']);
        }
        
        //Advance search
        if ($formInput['query']) {
            $query->where(function($query) use ($formInput) {

                //Workshop search
                $query->whereHas('info', function ($q) use ($formInput) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                });

                //Program search
                $query->orWhereHas('programs.info', function ($q) use ($formInput) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                });

            });
        }

        $query->has('programs', '>', 0);

        if ($formInput['date']) {
            $query->whereHas('programs', function ($q) use ($formInput) {
                $q->where('start_date', $formInput['date']);
            });
        }
        
        $query->whereNull('deleted_at')
            ->orderBy('date', 'ASC')
            ->orderBy('start_time', 'ASC')
            ->orderBy('end_time', 'ASC')
            ->orderBy('created_at', 'ASC');

        $workshops = $query->get()->toArray();

        foreach ($workshops as $key => $row) {
            $rowData = array();
            $infoData = readArrayKey($row, $rowData, 'info');
            $workshops[$key] = $row;
            $workshops[$key]['info'] = $infoData;
        }

        return $workshops;
    }

    /**
    * @param mixed $formInput
    * @return [type]
    */
    static public function getProgramDetail($formInput)
    {
        $query = \App\Models\EventAgenda::where('conf_event_agendas.event_id', '=', $formInput['event_id']);

        $query->with([
            'info' => function ($query) use ($formInput) {
                $query->where('languages_id', '=', $formInput['language_id']);
            },
            'program_speakers.info'
            , 'tracks.info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }
        ]);

        $query->where('conf_event_agendas.id', $formInput['program_id']);

        $query->whereNull('conf_event_agendas.deleted_at')
        ->select(array('conf_event_agendas.*'));

        $program = $query->first()->toArray();

        $rowData = array();

        $infoData = readArrayKey($program, $rowData, 'info');

        $rowData['id'] = $program['id'];
        $rowData['workshop_id'] = $program['workshop_id'];
        $rowData['topic'] = isset($infoData['topic']) ? $infoData['topic'] : '';
        $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
        $rowData['date'] = isset($infoData['date']) ? date('Y-m-d', strtotime($infoData['date'])) : '';
        $rowData['heading_date'] = \Carbon\Carbon::parse($rowData['date'])->format('l j. F Y');
        $rowData['start_time'] = isset($infoData['start_time']) ? $infoData['start_time'] : '';
        $rowData['end_time'] = isset($infoData['end_time']) ? $infoData['end_time'] : '';
        $rowData['location'] = isset($infoData['location']) ? $infoData['location'] : '';
        $rowData['start_date_time'] = \Carbon\Carbon::parse($rowData['date'].' '.$rowData['start_time'])->toDateTimeString();

        //program speakers
        $program_speakers = array();

        if (count($program['program_speakers'] ?? []) > 0) {
            foreach ($program['program_speakers'] as $speaker) {
                $speaker['info'] = readArrayKey($speaker, [], 'info');
                $program_speakers[] = $speaker;
            }
        }
        $rowData['program_speakers'] = $program_speakers;

        //program tracks
        $program_tracks = array();
        if (count($program['tracks'] ?? []) > 0) {
            foreach ($program['tracks'] as $track) {
                $info = readArrayKey($track, [], 'info');
                $program_tracks[] = $info;
            }
        }

        $rowData['program_tracks'] = $program_tracks;

        $program = $rowData;
            
        return $program;
    }

    /**
     * @param mixed $formInput
     * @return [type]
     */
    public function getProgramSpeakers($formInput)
    {
        $filter=!empty($formInput['sort_by'])?$formInput['sort_by']:'first_name';
        $result = \App\Models\EventAgendaSpeaker::where('agenda_id', $formInput['id'])->where('event_id', $formInput['event_id']);

        $result->join('conf_attendees', 'conf_attendees.id', '=', 'conf_event_agenda_speakers.attendee_id')
        ->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_attendees.id')
        ->where('conf_attendees_info.languages_id', '=', $formInput['language_id']);
        if(isset($formInput['query']) && $formInput['query']){
            $search=$formInput['query'];
            $result->where(function ($query) use ($search, $filter) {
               
                if ($filter == 'first_name') {
                    $query->where(\DB::raw('CONCAT(conf_attendees.first_name, " ", conf_attendees.last_name)'), 'LIKE', '%' . trim($search) . '%');
                }
    
                if ($filter == 'email') {
                    $query->where('conf_attendees.email', 'like', '%' . $search . '%');
                }
                if ($filter == 'company_name') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'company_name')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }
    
                if ($filter == 'delegate') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'delegate_number')
                            ->where('conf_attendees_info.value', '=', trim($search));
                    });
                }
    
                if ($filter == 'department') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'department')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }
    
                if ($filter == 'title') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'title')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }
    
                if ($filter == 'network_group') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'network_group')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }
    
            });
        }
        
        //Attendee Info
		$result->with(['attendee.info' => function ($query) use ($formInput) {
			return $query->where(function ($query) {
				return $query->where('name', '=', 'title')
					->orWhere('name', '=', 'company_name')
					->orWhere('name', '=', 'network_group')
					->orWhere('name', '=', 'table_number')
					->orWhere('name', '=', 'phone')
					->orWhere('name', '=', 'website')
					->orWhere('name', '=', 'allow_vote')
					->orWhere('name', '=', 'ask_to_apeak')
					->orWhere('name', '=', 'initial')
					->orWhere('name', '=', 'delegate_number')
					->orWhere('name', '=', 'department')
					->orWhere('name', '=', 'allow_gallery');
			})->where('languages_id', '=', $formInput['language_id']);
		}]);
        
        $program_attendees = $result->groupBy('conf_attendees.id')->paginate($formInput['limit'])->toArray();

        foreach ($program_attendees['data'] as $key => $row) {
            
            $response = readArrayKey($row['attendee'], [], 'info');
            
            $row['attendee_detail'] = $response;

            $program_attendees['data'][$key] = $row;
        }

        return $program_attendees;
    }

    /**
	 * detachedProgram
	 *
	 * @param  mixed $formInput
	 * @return void
	 */
	public function detachedProgram($formInput)
	{
		\App\Models\EventAgendaSpeaker::where('attendee_id', $formInput['attendee_id'])->where('agenda_id', $formInput['agenda_id'])->where('event_id', $formInput['event_id'])->delete();
	}

    /**
	 * attachProgram
	 *
	 * @param  mixed $formInput
	 * @return void
	 */
	public function attachProgram($formInput)
	{
        $record = \App\Models\EventAgendaSpeaker::where('event_id', '=', $formInput['event_id'])->where('attendee_id', $formInput['attendee_id'])->where('agenda_id', $formInput['agenda_id'])->first();
        if(!$record) {
            $max = \App\Models\EventAgendaSpeaker::where('event_id', '=', $formInput['event_id'])->max('sort_order');
            $data['attendee_id'] = $formInput['attendee_id'];
            $data['agenda_id'] = $formInput['agenda_id'];
            $data['event_id'] = $formInput['event_id'];
            $data['sort_order'] = ($max + 1);
            \App\Models\EventAgendaSpeaker::create($data);
        }
	}

    /**
     * Workshop programs
     * @param array
     */
    static public function workshopPrograms($formInput)
    {
        $query = \App\Models\EventAgenda::where('conf_event_agendas.event_id', '=', $formInput['event_id']);

        $query->leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
            $join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
                ->where('a_end_time.name', '=', 'end_time')
                ->where('a_end_time.languages_id', '=', $formInput['language_id']);
        });

        $query->with([
            'info' => function ($query) use ($formInput) {
                $query->where('languages_id', '=', $formInput['language_id']);
            }, 'program_speakers.info' => function ($query) use ($formInput) {
                $query->where('languages_id', '=', $formInput['language_id']);
            }, "program_speakers.currentEventAttendee",
            'program_workshop.info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }, 'tracks.info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }
        ]);

        $query->where('conf_event_agendas.workshop_id', $formInput['workshop_id']);

        //myFavs
        if ($formInput['favs']) {
            $agenda_array = [];
            $attendee_agendas = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', '=', $formInput['attendee_id'])->whereNull('deleted_at')->get()->toArray();
            foreach ($attendee_agendas as $r) {
                $agenda_array[]=$r['agenda_id'];
            }
            $settings = \App\Models\AgendaSetting::where('event_id', '=', $formInput['event_id'])->first();
            if ($settings['enable_program_attendee'] == 1) {
                $attendeeGroups = \App\Models\EventAttendeeGroup::where('attendee_id', '=', $formInput['attendee_id'])->whereHas('group', function ($query) {
                    return $query->where('status', '=', 1);
                })->with('group.assignAgendaGroups')->get()->toArray();
                foreach ($attendeeGroups as $attendeeGroup) {
                    foreach ($attendeeGroup['group']['assign_agenda_groups'] as $program) {
                        $agenda_array[] = $program['id'];
                    }
                }
            }
            $query->whereIn('conf_event_agendas.id', $agenda_array);
        }


        //speaker attached
        if ($formInput['speaker_id']) {
            $query->whereHas('program_speakers', function ($q) use ($formInput) {
                $q->where('conf_event_agenda_speakers.attendee_id', $formInput['speaker_id']);
            });
        }

        //Track
        if ($formInput['track_id']) {
            $query->whereHas('tracks', function ($q) use ($formInput) {
                $q->where('conf_event_agenda_tracks.track_id', $formInput['track_id']);
            });
        }
        
        //Workshop
        if ($formInput['workshop_id']) {
            $query->whereHas('program_workshop', function ($q) use ($formInput) {
                $q->where('workshop_id', $formInput['workshop_id']);
            });
        }

        //Program id
        if ($formInput['program_id']) {
            $query->where('conf_event_agendas.id', $formInput['program_id']);
        }
        
        //Advance search
        $query->where(function($query) use ($formInput) {

            //Program search
            $query->whereHas('info', function ($q) use ($formInput) {
                if ($formInput['query']) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                }
            });

            //Workshop search
            $query->orWhereHas('program_workshop.info', function ($q) use ($formInput) {
                if ($formInput['query']) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                }
            });

            //Tracks search
            $query->orWhereHas('tracks.info', function ($q) use ($formInput) {
                if ($formInput['query']) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                }
            });

            //Attendee search
            $query->orWhereHas('program_speakers', function ($q) use ($formInput) {
                if ($formInput['query']) {
                    $q->where(\DB::raw('CONCAT(conf_attendees.first_name, " ", conf_attendees.last_name)'), 'LIKE', '%' . trim($formInput['query']) . '%');
                }
            });

            $query->orWhereHas('program_speakers.info', function ($q) use ($formInput) {
                if ($formInput['query']) {
                    $q->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%')->whereIn('conf_attendees_info.name', ['company_name', 'title']);
                }
            });
        });
        
        if ($formInput['date']) {
            $query->whereDate('conf_event_agendas.start_date', $formInput['date']);
        }
        
        $query->whereNull('conf_event_agendas.deleted_at')
        ->orderBy('conf_event_agendas.start_date', 'ASC')
        ->orderBy('conf_event_agendas.start_time', 'ASC')
        ->orderBy('end_time', 'ASC')
        ->orderBy('conf_event_agendas.created_at', 'ASC')
        ->groupBy('conf_event_agendas.id')
        ->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'));

        $programs = $query->get()->toArray();

        foreach ($programs as $key => $row) {
            $rowData = array();
            $infoData = readArrayKey($row, $rowData, 'info');
            $rowData['id'] = $row['id'];
            $rowData['hide_time'] = $row['hide_time'];
            $rowData['topic'] = isset($infoData['topic']) ? $infoData['topic'] : '';
            $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
            $rowData['date'] = isset($infoData['date']) ? date('Y-m-d', strtotime($infoData['date'])) : '';
            $rowData['heading_date'] = \Carbon\Carbon::parse($rowData['date'])->format('l j. F Y');
            $rowData['start_time'] = isset($infoData['start_time']) ? $infoData['start_time'] : '';
            $rowData['end_time'] = isset($infoData['end_time']) ? $infoData['end_time'] : '';
            $rowData['location'] = isset($infoData['location']) ? $infoData['location'] : '';

            //program speakers
            $program_speakers = array();
            if (count($row['program_speakers'] ?? []) > 0) {
                foreach ($row['program_speakers'] as $speaker) {
                    $attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'speaker', $formInput['language_id']);
					$speaker = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $speaker, $attendee_type_id, $speaker["current_event_attendee"]["gdpr"]);
					$speaker['info'] = readArrayKey(['info' => $speaker['info']], [], 'info');
                    $program_speakers[] = $speaker;
                }
            }
            $rowData['program_speakers'] = $program_speakers;

            //program tracks
            $program_tracks = array();
            if (count($row['tracks'] ?? []) > 0) {
                foreach ($row['tracks'] as $track) {
                    $info = readArrayKey($track, [], 'info');
                    $program_tracks[] = $info;
                }
            }
            
            $rowData['program_tracks'] = $program_tracks;

            $programs[$key] = $rowData;
        }
            
        return $programs;
    }

    /**
     * Advance search programs
     * @param array
     */
    static public function search($formInput, $pagination = true)
    {
        $query = \App\Models\EventAgenda::where('conf_event_agendas.event_id', '=', $formInput['event_id']);

        $query->leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
            $join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
                ->where('a_end_time.name', '=', 'end_time')
                ->where('a_end_time.languages_id', '=', $formInput['language_id']);
        });
        
        $query->with([
            'info' => function ($query) use ($formInput) {
                $query->where('languages_id', '=', $formInput['language_id']);
            }
            ,'program_speakers.info'
            ,'program_speakers.currentEventAttendee'
            ,'program_speakers' => function ($query) use ($formInput) {
                if($formInput['event']['speaker_settings']['order_by'] == 'custom') {
                    return $query->orderBy('agenda_speaker_sort', 'ASC')->orderBy('sort_order', 'ASC');
                } else if($formInput['event']['speaker_settings']['order_by'] == 'first_name') {
                    return $query->orderBy('agenda_speaker_sort', 'ASC')->orderBy('first_name', 'ASC')->orderBy('last_name', 'ASC');
                } else if($formInput['event']['speaker_settings']['order_by'] == 'last_name') {
                    return $query->orderBy('agenda_speaker_sort', 'ASC')->orderBy('last_name', 'ASC')->orderBy('first_name', 'ASC');
                } else if(isset($formInput['event']['speaker_settings']['order_by']) && $formInput['event']['speaker_settings']['order_by']) {
                    return $query->orderBy($formInput['event']['speaker_settings']['order_by'], 'ASC');
                } else {
                    return $query->orderBy('id', 'DESC');
                }
            },
            'program_workshop.info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }, 'tracks.info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }
        ]); 

        //myFavs
        if ($formInput['favs']) {
            $agenda_array = [];
            $attendee_agendas = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', '=', $formInput['attendee_id'])->whereNull('deleted_at')->get()->toArray();
            foreach ($attendee_agendas as $r) {
                $agenda_array[]=$r['agenda_id'];
            }
            $settings = \App\Models\AgendaSetting::where('event_id', '=', $formInput['event_id'])->first();
            if ($settings['enable_program_attendee'] == 1) {
                $attendeeGroups = \App\Models\EventAttendeeGroup::where('attendee_id', '=', $formInput['attendee_id'])->whereHas('group', function ($query) {
                    return $query->where('status', '=', 1);
                })->with('group.assignAgendaGroups')->get()->toArray();
                foreach ($attendeeGroups as $attendeeGroup) {
                    foreach ($attendeeGroup['group']['assign_agenda_groups'] as $program) {
                        $agenda_array[] = $program['id'];
                    }
                }
            }
            $query->whereIn('conf_event_agendas.id', $agenda_array);
        }

        // exclude speaker list program
        if($formInput['excludeSpeakerList']){
            $query->where('only_for_speaker_list', 0);
        }

        // hide on webapp 
        if($formInput['hide_on_app']){
            $query->where('hide_on_app', 0);
        }

        // exclude qa list program 
        if($formInput['only_for_qa']){
            $query->where('only_for_qa',0);
        }
        
        // hide on reg.Site list program 
        if($formInput['hide_on_registrationsite']){
            $query->where('hide_on_registrationsite',0);
        }

        //Workshop
        if ($formInput['workshop_id']) {
            $query->whereHas('program_workshop', function ($q) use ($formInput) {
                $q->where('workshop_id', $formInput['workshop_id']);
            });
        } else {
            if(!$formInput['webAppListing']){
                $query->where('conf_event_agendas.workshop_id', 0);
            }
        }

        // tracks
        if ($formInput['track_id']) {
            // tracksListing
            if($formInput['trackListing']){
                $query->where(function($query) use ($formInput) {
                    $query->whereHas('tracks', function ($q) use ($formInput) {
                        $q->where('conf_event_agenda_tracks.track_id', $formInput['track_id']);
                    })
                    ->orWhereDoesnthave('tracks');
                });
            }else{
                $query->whereHas('tracks', function ($q) use ($formInput) {
                    $q->where('conf_event_agenda_tracks.track_id', $formInput['track_id']);
                });
            }
        }

        //Program id
        if ($formInput['program_id']) {
            $query->where('conf_event_agendas.id', $formInput['program_id']);
        }
        
        //Advance search
        if ($formInput['query']) {

            $query->where(function($query) use ($formInput) {

                //Program search
                $query->whereHas('info', function ($q) use ($formInput) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                });

                //Workshop search
                $query->orWhereHas('program_workshop.info', function ($q) use ($formInput) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                });

                //Tracks search
                $query->orWhereHas('tracks.info', function ($q) use ($formInput) {
                    $q->where('value', 'LIKE', '%' . trim($formInput['query']) . '%');
                });

                //Attendee search
                $query->orWhereHas('program_speakers', function ($q) use ($formInput) {
                    $q->where('conf_attendees.first_name', 'LIKE', '%' . trim($formInput['query']) . '%')
                    ->orWhere('conf_attendees.last_name', 'LIKE', '%' . trim($formInput['query']) . '%');
                });

                $query->orWhereHas('program_speakers.info', function ($q) use ($formInput) {
                    $q->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%')->whereIn('conf_attendees_info.name', ['company_name', 'title']);
                });

            });
        }
        
        if ($formInput['date']) {
            $query->whereDate('conf_event_agendas.start_date', $formInput['date']);
        }
        
        $query->whereNull('conf_event_agendas.deleted_at')
        ->where('conf_event_agendas.only_for_poll','=',0)
        ->orderBy('conf_event_agendas.start_date', 'ASC')
        ->orderBy('conf_event_agendas.start_time', 'ASC')
        ->orderBy('end_time', 'ASC')
        ->orderBy('conf_event_agendas.id', 'ASC')
        ->groupBy('conf_event_agendas.id')
        ->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'));

        if ($pagination) {
            $programs = $query->paginate($formInput['limit'])->toArray();
        } else {
            $programs['data'] = $query->get()->toArray();
        }

        foreach ($programs['data'] as $key => $row) {
            $rowData = array();
            $infoData = readArrayKey($row, $rowData, 'info');
            $rowData['id'] = $row['id'];
            $rowData['hide_time'] = $row['hide_time'];
            $rowData['only_for_speaker_list'] = $row['only_for_speaker_list'];
            $rowData['workshop_id'] = $row['workshop_id'];
            $rowData['topic'] = isset($infoData['topic']) ? $infoData['topic'] : '';
            $rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';
            $rowData['date'] = isset($infoData['date']) ? date('Y-m-d', strtotime($infoData['date'])) : '';
            $rowData['heading_date'] = \Carbon\Carbon::parse($rowData['date'])->format('l j. F Y');
            $rowData['start_time'] = isset($infoData['start_time']) ? $infoData['start_time'] : '';
            $rowData['end_time'] = isset($infoData['end_time']) ? $infoData['end_time'] : '';
            $rowData['location'] = isset($infoData['location']) ? $infoData['location'] : '';
            $rowData['start_date_time'] = \Carbon\Carbon::parse($rowData['date'].' '.$rowData['start_time'])->toDateTimeString();

            //program speakers
            $program_speakers = array();
            if (count($row['program_speakers'] ?? []) > 0) {
                $speaker_settings = \App\Models\SpeakerSetting::where('event_id', '=', $formInput['event_id'])->get()->toArray();
                foreach ($row['program_speakers'] as $speaker) {
                    $attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'speaker', $formInput['language_id']);
					$speaker = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $speaker, $attendee_type_id, $speaker["current_event_attendee"]["gdpr"]);
					$speaker['info'] = readArrayKey(['info' => $speaker['info']], [], 'info');
                    $program_speakers[] = $speaker;
                }
            }
            $rowData['program_speakers'] = $program_speakers;

            //program tracks
            $program_tracks = array();
            if (count($row['tracks'] ?? []) > 0) {
                foreach ($row['tracks'] as $track) {
                    $info = readArrayKey($track, [], 'info');
                    $program_tracks[] = $info;
                }
            }

            //program workshop
            if (count($row['program_workshop']['info'] ?? []) > 0) {
                foreach ($row['program_workshop']['info'] as $val) {
                    if($val['name'] == "name") {
                        $rowData['program_workshop'] = $val['value'];
                    }
                }
                $rowData['program_workshop_start_time'] = $row['program_workshop']['start_time'];
                $rowData['program_workshop_end_time'] = $row['program_workshop']['end_time'];
            }

            $rowData['program_tracks'] = $program_tracks;
            
            //Workshop programs
            $programs['data'][$key] = $rowData;
        }
            
        return $programs;
    }

    /**
     * getAllTracks
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getAllTracks($formInput)
    {
        $searchKey = '';

        if ($formInput['searchText']) {
            $searchKey = '%' . $formInput['searchText'] . '%';
        }

        $event_id = $formInput['event_id'];
        $language_id = $formInput['language_id'];

        $result = \App\Models\EventTrack::where('event_id', '=', $formInput['event_id'])->where('parent_id','=','0')->with(['info' => function ($query) use($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }]);
        
        if($searchKey) {
          $result =  $result->whereHas('info', function ($q) use ($searchKey) {
                    $q->where('value', 'like', $searchKey);
            });
        }

       $result = $result->whereNull('deleted_at')->orderBy('sort_order');

       if($formInput['limit']){
            $result = $result->paginate($formInput['limit'])->toArray();
        }else{
           $result = $result->get()->toArray();
       }

        return $result;
    }
    
    public function getSubTracksAttached($formInput)
    {
        $track_id = $formInput['track_id'];
        $event_id = $formInput['event_id'];
        $language_id = $formInput['language_id'];
        $searchKey = (isset($formInput['searchText']) && $formInput['searchText'] !== "") ? '%'. $formInput['searchText']. '%' : null;
        $subtracks = \App\Models\EventAgendaTrack::whereHas('tracks', function($query) use($track_id, $event_id){
            return $query->where('event_id', $event_id)->where('parent_id','=', $track_id)->orderBy('sort_order');
        })->with(['tracks.info' => function ($query) use($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }]);

        if($searchKey !== null){
            $subtracks = $subtracks->whereHas('tracks.info', function ($q) use ($searchKey) {
                   return $q->where('value', 'like', $searchKey);
            });
        }

        $subtracks = $subtracks->whereNull('deleted_at')->groupby('track_id')->get()->toArray();
        return $subtracks;
    }

}