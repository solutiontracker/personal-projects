<?php

namespace App\Http\Controllers\Mobile;

use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\MyTurnListRepository;
use App\Eventbuizz\Repositories\ProgramRepository;
use App\Eventbuizz\Repositories\CheckInOutRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProgramController extends Controller
{
    public $successStatus = 200;

    protected $programRepository;

    protected $eventSettingRepository;

    protected $myTurnListRepository;

    protected $attendeeRepository;

    /**
     * @param ProgramRepository $programRepository
     * @param EventSettingRepository $eventSettingRepository
     * @param MyTurnListRepository $myTurnListRepository
     * @param AttendeeRepository $attendeeRepository
     */
    public function __construct(ProgramRepository $programRepository, EventSettingRepository $eventSettingRepository, MyTurnListRepository $myTurnListRepository, AttendeeRepository $attendeeRepository)
    {
        $this->programRepository = $programRepository;
        $this->eventSettingRepository = $eventSettingRepository;
        $this->myTurnListRepository = $myTurnListRepository;
        $this->attendeeRepository = $attendeeRepository;
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function videos(Request $request)
    {
        $event = $request->event;

        $attendee_id = $request->attendee_id;

        $url = ($request->url ? $request->url : '');

        $request_program_id = ($request->program_id ? $request->program_id : '');

        $program_id = ($request->program_id ? $request->program_id : '');

        $is_live = ($request->is_live ? $request->is_live : '');

        $is_iframe = 0;

        $plateform = ($request->plateform ? $request->plateform : '');

        $type = ($request->type ? $request->type : '');

        $current_video = ($request->current_video ? $request->current_video : '');

        //Request modified
        $request->merge([
            "alias" => "checkIn",
            "program_id" => $program_id,
        ]);

        $program_setting = $this->programRepository->getSetting($request->all());

        $program_videos = $this->programRepository->videos($request->all());

        $attendee_groups = (array) $this->attendeeRepository->getAttendeeGroupsIds($request->all());

        //parse embed urls
        foreach ($program_videos as $p_key => $program) {

            //Attached attendees
            $request->merge(["program_id" => $program['id']]);

            $attachedAttendees = $this->programRepository->attachedAttendees($request->all(), true);

            $groups = (array) $this->programRepository->getAgendaGroupsIds($request->all());
            
            foreach ($program['videos'] as $v_key => $video) {

                if ($video['status'] == 1) {

                    if ((((in_array($video["type"], ['link', 'local', 'live', 'agora-realtime-broadcasting-custom', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) && $video["private"] == 1) || $video["type"] == "agora-rooms") && ($attachedAttendees > 0 || ($program_setting['enable_program_attendee'] == 1 && count(array_intersect($groups, $attendee_groups)) > 0))) || (in_array($video["type"], ['agora-panel-disscussions']) && ($attachedAttendees > 0 || (count(array_intersect($groups, $attendee_groups)) > 0 || $video["private"] == 0))) || ($video["private"] == 0 && $video["type"] != "agora-rooms")) {
                        if ($video['url'] || $video['filename'] || (in_array($video['type'], ['agora-realtime-broadcasting-custom', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions']) && $video['name'])) {
                            $info = parse_video($video, $event['url'], $attendee_id, $attachedAttendees, $event['settings']);
                            if ($info['iframe'] == 1 && $info['url']) {
                                if (!$url && !$request_program_id) {
                                    $url = $info['url'];
                                    $current_video = $video['id'];
                                    $program_id = $program['id'];
                                    $is_live = $video['is_live'];
                                    $plateform = $video['plateform'];
                                    $type = $video['type'];
                                    $is_iframe = 1;
                                }
                                $program_videos[$p_key]['videos'][$v_key]['url'] = $info['url'];
                                $program_videos[$p_key]['videos'][$v_key]['is_iframe'] = 1;
                            } else {
                                $program_videos[$p_key]['videos'][$v_key]['is_iframe'] = 0;
                            }

                            if ($video['thumbnail']) {
                                if (\App::environment('local')) {
                                    $program_videos[$p_key]['videos'][$v_key]['image'] = cdn('assets/program/videos/thumbnails/' . $video['thumbnail']);
                                } else {
                                    $program_videos[$p_key]['videos'][$v_key]['image'] = getS3Image('assets/program/videos/thumbnails/' . $video['thumbnail']);
                                }
                            } else {
                                $program_videos[$p_key]['videos'][$v_key]['image'] = "";
                            }
                        }
                    } else {
                        $program_videos[$p_key]['videos'][$v_key]['status'] = 0;
                    }

                }
            }

            $program_videos[$p_key]['videos_count'] = collect($program_videos[$p_key]['videos'])->where('status', 1)->count();

            $program_videos[$p_key]['checkin_status'] = CheckInOutRepository::checkInOutStatus(['attendee_id' => $attendee_id, 'event_id' => $event['id'], 'language_id' => $event['language_id'], 'organizer_id' => $request->organizer_id, 'type_id' => $program['id'], 'type_name' => 'program']);
        }
        
        $request->merge(["program_id" => ($request_program_id ? $request_program_id : $program_id)]);

        $attachedAttendees = $this->programRepository->attachedAttendees($request->all(), true);

        $groups = (array) $this->programRepository->getAgendaGroupsIds($request->all());

        $program_data = $this->programRepository->getProgram($request->all());

        if ($program_data) {

            //parse embed urls
            if ($program_data['info']['videos']) {

                foreach ($program_data['info']['videos'] as $v_key => $video) {
                    if ($video['status'] == 1) {
                        if ((((in_array($video["type"], ['link', 'local', 'live', 'agora-realtime-broadcasting-custom', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) && $video["private"] == 1) || $video["type"] == "agora-rooms") && ($attachedAttendees > 0 || ($program_setting['enable_program_attendee'] == 1 && count(array_intersect($groups, $attendee_groups)) > 0))) || (in_array($video["type"], ['agora-panel-disscussions']) && ($attachedAttendees > 0 || (count(array_intersect($groups, $attendee_groups)) > 0 || $video["private"] == 0))) || ($video["private"] == 0 && $video["type"] != "agora-rooms")) {
                            if ($video['url'] || $video['filename'] || (in_array($video['type'], ['agora-realtime-broadcasting-custom', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions']) && $video['name'])) {
                                $info = parse_video($video, $event['url'], $attendee_id, $attachedAttendees, $event['settings']);
                                if ($info['iframe'] == 1 && $info['url']) {
                                    if (!$url) {
                                        $url = $info['url'];
                                        $current_video = $video['id'];
                                        $is_live = $video['is_live'];
                                        $plateform = $video['plateform'];
                                        $type = $video['type'];
                                        $is_iframe = 1;
                                    }
                                    $program_data['info']['videos'][$v_key]['url'] = $info['url'];
                                    $program_data['info']['videos'][$v_key]['is_iframe'] = 1;
                                } else {
                                    $program_data['info']['videos'][$v_key]['is_iframe'] = 0;
                                }

                                if ($video['thumbnail']) {
                                    if (\App::environment('local')) {
                                        $program_data['info']['videos'][$v_key]['image'] = cdn('assets/program/videos/thumbnails/' . $video['thumbnail']);
                                    } else {
                                        $program_data['info']['videos'][$v_key]['image'] = getS3Image('assets/program/videos/thumbnails/' . $video['thumbnail']);
                                    }
                                } else {
                                    $program_data['info']['videos'][$v_key]['image'] = "";
                                }
                            }
                        } else {
                            $program_data['info']['videos'][$v_key]['status'] = 0;
                        }
                    }
                }

            }

            $program_data['info']['videos_count'] = collect($program_data['info']['videos'])->where('status', 1)->count();
            
            $attendee = $this->myTurnListRepository->getAttendeeDetail($request->all());

            $session = $this->myTurnListRepository->getActiveSession($request->all());
        }

        //groups program by date
        $program_videos = collect($program_videos, "id")->groupBy('start_date')->all();

        $checkin_settings = CheckInOutRepository::getSetting(['event_id' => $event['id']]);

        return response()->json([
            'success' => true,
            'data' => array(
                "program_videos" => $program_videos,
                "url" => $url,
                "is_live" => $is_live,
                "is_iframe" => $is_iframe,
                "program_setting" => $program_setting,
                "program_data" => $program_data,
                "is_live" => $is_live,
                "current_video" => $current_video,
                "attendee" => $attendee,
                "session" => $session,
                "plateform" => $plateform,
                "type" => $type,
                "checkin_settings" => $checkin_settings,
            ),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function timetable(Request $request)
    {
        $program_array = array();

        $program_workshops = array();

        $programs = $this->programRepository->listing($request->all(), false);
        
        $program_tracks = $this->programRepository->tracks($request->all());

        $key = 0;
        foreach ($programs as $program) {
            $rowData = array();
            $infoData = readArrayKey($program, $rowData, 'info');
            $count_videos = count($program['videos']);
            $count_meetings = count(Arr::where($program['videos'], function ($row, $key) {
                return ((in_array($row["type"], ['link', 'local', 'live', 'agora-realtime-broadcasting-custom', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) && $row["private"] == 1 ) || $row["type"] == "agora-rooms");
            }));
            //Attached attendees
            $request->merge(["program_id" => $program['id']]);
            $attachedAttendees = $this->programRepository->attachedAttendees($request->all(), true);
            $program_array[$key]['id'] = $program['id'];
            $program_array[$key]['name'] = isset($infoData['topic']) ? $infoData['topic'] : '';
            $program_array[$key]['workshop'] = $program['program_workshop']['info'][0]['value'];
            $program_array[$key]['date'] = \Carbon\Carbon::parse($program['date'])->format('d-m-Y');
            $program_array[$key]['start_time'] = \Carbon\Carbon::parse($program['start_time'])->format('H:i');
            $program_array[$key]['end_time'] = \Carbon\Carbon::parse($program['end_time'])->format('H:i');
            if (($count_videos == $count_meetings && $attachedAttendees > 0) || $count_videos != $count_meetings || $count_videos == 0) {
                if ($attachedAttendees == 0 && $count_videos != $count_meetings && $count_videos > 0) {
                    $program_array[$key]['video'] = $count_videos - $count_meetings;
                } else {
                    $program_array[$key]['video'] = $count_videos;
                }
            } else {
                $program_array[$key]['video'] = 0;
            }
            $program_array[$key]['count_meetings'] = $count_meetings;
            $program_array[$key]['attachedAttendees'] = $attachedAttendees;
            if ($program_array[$key]['workshop']) {
                array_push($program_workshops, $program_array[$key]['workshop']);
            }

            //program tracks
            if (count($program['tracks']) > 0) {
                $temp_tracks = array();
                $k = 0;
                foreach ($program['tracks'] as $track) {
                    $temp_tracks[] = $track['info'][0]['value'];
                    $k++;
                }

                $program_array[$key]['tracks'] = $temp_tracks;
            } else {
                $program_array[$key]['tracks'] = null;
            }

            $key++;
        }

        $program_workshops = array_values(array_unique($program_workshops));

        $program_setting = $this->programRepository->getSetting($request->all());

        $schedules = $this->programRepository->schedules($request->all());

        //current date / time
        $current_date = getClosestNextDate($schedules, \Carbon\Carbon::now()->toDateString());
        $current_date = ($current_date ? $current_date : end($schedules));
        $selected_date = ($request->date ? $request->date : $current_date);
        $current_time = \Carbon\Carbon::now()->toTimeString();

        return response()->json([
            'success' => true,
            'data' => array(
                "program_array" => $program_array,
                "program_workshops" => $program_workshops,
                "program_tracks" => $program_tracks,
                "program_setting" => $program_setting,
                "schedules" => $schedules,
                "current_date" => $current_date,
                "selected_date" => $selected_date,
                "current_time" => $current_time,
            ),
        ], $this->successStatus);
    }
    
    /**
     * ivsStream
     *
     * @param  mixed $request
     * @param  mixed $program_id
     * @param  mixed $code
     * @return void
     */
    public function ivsStream(Request $request, $program_id, $code)
    {
        $video = ProgramRepository::getProgramVideo(["agenda_id" => $program_id, "id" => $code]);

        $environment = \App::environment();

        if(in_array($video->type, ["agora-realtime-broadcasting-custom"])) {
            $src = $video->url;
        }

        return \View::make('mobile.program.video.ivs-stream', compact('src', 'video'));  
    }
    
    /**
     * onDemandStream
     *
     * @param  mixed $request
     * @param  mixed $program_id
     * @param  mixed $code
     * @return void
     */
    public function onDemandStream(Request $request, $program_id, $code)
    {
        $video = ProgramRepository::getProgramVideo(["agenda_id" => $program_id, "id" => $code]);

        $environment = \App::environment();

        if(in_array($environment, ["local"])) {
            $src = cdn('assets/program/videos/' . $video->filename);
            $thumbnail = cdn('assets/program/videos/thumbnails/' . '860-'.$video->thumbnail);
        } else {
            $src = getS3Image('assets/program/videos/' . $video->filename);
            $thumbnail = getS3Image('assets/program/videos/thumbnails/' . '860-'.$video->thumbnail);
        }
        
        return \View::make('mobile.program.video.on-demand-stream', compact('src', 'thumbnail'));  
    }
}
