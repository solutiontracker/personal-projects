<?php

namespace App\Http\Controllers\WebApp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\ProgramRepository;

class ProgramController extends Controller
{
    public $successStatus = 200;

    /**
     * __construct
     *
     * @param  mixed $programRepository
     * @return void
     */
    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    public function index(Request $request, $event_id)
    {
        $myturnlist = isset($request["myturnlist"]) && $request["myturnlist"] == "true"  ? "true" : "false";
        $prevDate = isset($request["prevDate"]) && $request["prevDate"] !== ""  ? $request["prevDate"] : "";
        $sortBy = 'id';
        $order = 'asc';
        $page = isset($request['page']) ? $request['page'] : 1;
        $attendee_id = $request['attendee_id'];
        $default_listing = isset($request['default']) ? true  : false;
        $favs = (isset($request['fav']) && $request['fav'] !== "") ? $request['fav']  : "";
        $polls = isset($request['head']) ? $request['head'] : "";
        $tracks = isset($request['filter']) ? $request['filter'] : "";
        $searchTerm = (isset($request['searchText']) && $request['searchText'] !== "") ? $request['searchText']  : "";
        $workshop_clause = (isset($request['workshop_id']) && trim($request['workshop_id']) !== "") ? $request['workshop_id'] : 0;
        $workshop_id = (isset($request['workshop_id']) && trim($request['workshop_id']) !== "") ? $request['workshop_id'] : 0;
        $track_id= (isset($request['trackId']) && trim($request['trackId']) !== "") ? $request['trackId'] : 0;

        $event = \App\Models\Event::where('id', $event_id)->first()->toArray();
        $event_settings     = \App\Models\EventSetting::select(['name', 'value'])->where('event_id', $event_id)->pluck('value', 'name')->toArray();
        $event['labels'] = eventsite_labels(['eventsite', 'generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
        $language_id = $event['language_id'];
        if ($default_listing) {
            if ($event_settings['agenda_list'] == 'track') {
                return [
                    "success" => true,
                    "redirect" => "track",
                ];
                return response()->json([
                    'success' => true,
                    "data" => [
                        "redirect" => "track",
                    ]
                ], $this->successStatus);
            }
        }
        $programs = array();

        //Program setting 
        $program_setting = ProgramRepository::getSetting(["event_id" => $event['id']]);

        $request->merge([
            'limit' =>  $request->limit ? $request->limit : 40,
            "event_id" => $event_id,
            "language_id" => $language_id,
            "excludeSpeakerList" => true,
            "webAppListing" => true,
        ]);
        if ($favs === "yes") {
            $request->merge(["favs" => true,"hide_on_app" => true]);
        }else{
            $request->merge(["hide_on_app" => true, "only_for_qa" => true]);
        }
        if ($workshop_id > 0) {
            $request->merge(['workshop_id' => $workshop_id]);
        }
        if ($track_id > 0) {
            $request->merge(['track_id' => $track_id, "trackListing" => true,]);
        }
        if ($searchTerm !== "") {
            $request->merge(["query" => $searchTerm]);
        }

        $programs = $this->programRepository->search($request->all(), true);
        $total = $programs['total'];
        $workshopRefrence = (isset($request['workshopRefrence']) && $request['workshopRefrence'] !== null)  ? json_decode($request['workshopRefrence']) :[];
        if (!isset($request['workshop']) && $workshop_id === 0) {
            $newPrograms = [];
            foreach ($programs['data'] as $key => $program) {
                if ($program['workshop_id'] > 0) {
                    if (!in_array($program['workshop_id'], $workshopRefrence)) {
                        $request->merge(['workshop_id' => $program['workshop_id']]);
                        $workshop = $this->programRepository->workshops($request->all());
                        
                        if ($workshop > 0) {
                            $newPrograms[$key] = $workshop[0];
                            $newPrograms[$key]['program_workshop_web_app'] = $workshop[0]['info']['name'];
                            $newPrograms[$key]['start_date_time'] = \Carbon\Carbon::parse($workshop[0]['date'] . ' ' . $workshop[0]['start_time'])->toDateTimeString();
                        }
                        $workshopRefrence[] = $program['workshop_id'];
                    }
                }else{
                    $newPrograms[]=$program;
                }
            }
            $programs['data'] = $newPrograms; 
        } 
        elseif($workshop_id === 0) {
            foreach ($programs['data'] as $key => $program) {
                if ($program['workshop_id'] === 0) {
                    $newPrograms[]=$program;
                }
            }
            $programs['data'] = $newPrograms;
        }

        // dd($programs);


        $programs = array_values(collect($programs['data'], $programs['workshop_id'])->sortBy('start_date_time')->all());

        //groups program by date
        $programs = collect($programs, "date")->groupBy('date')->all();

        $agenda_array = [];
        $agenda_by_group_array=[];
        if ($request['attendee_id']) {
            $attendee_agendas = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', '=', $request['attendee_id'])->whereNull('deleted_at')->get()->toArray();
            foreach ($attendee_agendas as $r) {
                $agenda_array[] = $r['agenda_id'];
            }
            $settings = \App\Models\AgendaSetting::where('event_id', '=', $event_id)->first();
            if ($settings['enable_program_attendee'] == 1) {
                $attendeeGroups = \App\Models\EventAttendeeGroup::where('attendee_id', '=', $request['attendee_id'])->whereHas('group', function ($query) {
                    return $query->where('status', '=', 1);
                })->with('group.assignAgendaGroups')->get()->toArray();
                foreach ($attendeeGroups as $attendeeGroup) {
                    foreach ($attendeeGroup['group']['assign_agenda_groups'] as $program) {
                        $agenda_by_group_array[] = $program['id'];
                    }
                }
            }
        }
        $html = \View::make('web_app.program.agenda-list', compact('programs', 'event', 'program_setting', 'request', 'favs', 'myturnlist', 'agenda_array', "prevDate", "agenda_by_group_array"))->render();

        $prevDate = key(array_slice($programs, -1, 1, true));


        return response()->json([
            'success' => true,
            "data" => [
                'programs' => $programs,
                'workshopRefrence' => $workshopRefrence,
                'html' => $html,
                'prevDate' => $prevDate,
                'total' => $total,
            ]
        ], $this->successStatus);
    }


    public function agendas_by_tracks(Request $request, $event_id)
    {
        $event = \App\Models\Event::where('id', $event_id)->first()->toArray();
        $labels = eventsite_labels(['agendas', 'generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
        $request->merge(["event_id" => $event_id, "language_id" => $event['language_id'], 'limit'=> 40]);
        $tracks = $this->programRepository->getAllTracks($request->all());
        $total = $tracks['total'];
        $newTracks =[];
        $i=0;
        foreach ($tracks['data'] as $row) {
            $newTracks[$i]= $row;
            if (count($row['info']) > 0) {
                foreach ($row['info'] as $val) {
                    $newTracks[$i][$val['name']] = $val['value'];
                }
            }
            unset($newTracks[$i]['info']);
            $i++;
        }
        $tracks = $newTracks;
        
        $isFavorite = isset($request['fav']) && $request['fav'] === "yes" ? "?fav=yes" : '';
        $html = \View::make('web_app.program.track-list', compact('tracks', 'labels', 'event', 'isFavorite'))->render();
        return response()->json([
            'success' => true,
            "data" => [
                'tracks' => $tracks,
                'html' => $html,
                'total' => $total,
            ]
        ], $this->successStatus);
    }


    public function agendas_by_tracks_detail(Request $request,  $event_id, $track_id)
    {

        $prevDate = isset($request["prevDate"]) && $request["prevDate"] !== ""  ? $request["prevDate"] : "";
        $favs = isset($request['fav']) && $request['fav'] === "yes" ? $request['fav'] : '';
        $isFavorite = isset($request['fav']) && $request['fav'] === "yes" ? "?fav=yes" : '';
        $isTrack =  "&trackId=".$track_id;
        $event = \App\Models\Event::where('id', $event_id)->first()->toArray();
        $language_id = $event['language_id'];
        $event_settings     = \App\Models\EventSetting::select(['name', 'value'])->where('event_id', $event_id)->pluck('value', 'name')->toArray();
        $currentTrack = \App\Models\EventTrack::where('event_id', '=', $event_id)->where('parent_id','=','0')->where('id', $track_id)->with(['info' => function ($query) use($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }])->whereNull('deleted_at')->orderBy('sort_order')->first()->toArray();
        $currentTrack = readArrayKey($currentTrack, [], 'info');
        $labels = eventsite_labels(['agendas', 'generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);

        $request->merge([
            "event_id" => $event_id,
            "language_id" => $language_id,
            "track_id" => $track_id,
        ]);
        
        $subTracks = $this->programRepository->getSubTracksAttached($request->all());

        $newSubTracks = [];

        foreach ($subTracks as $key => $value) {
            $newSubTracks[$key] = readArrayKey($value['tracks'], [], 'info');
            $newSubTracks[$key]['track_id']= $value['track_id'];
        }
        $subTracks = $newSubTracks;
        
        $track_name = $currentTrack['name'];
        $track_color = $currentTrack['color'];
        
        $request->merge([
            "excludeSpeakerList" => true,
            "webAppListing" => true,
            "trackListing" => true,
            "limit" => 40,
        ]);
        
        if ($favs === "yes") {
            $request->merge(["favs" => true]);
        }else{
            $request->merge(["hide_on_app" => true, "only_for_qa" => true]);
        }
        
        if ($track_id > 0) {
            $request->merge(['track_id' => $track_id]);
        }
        
        if (isset($formInput['searchText'])  && $formInput['searchText'] !== "") {
            $request->merge(["query" => $formInput['searchText']]);
        }
        
        $programs = $this->programRepository->search($request->all(), true);

        $workshopRefrence = (isset($request['workshopRefrence']) && $request['workshopRefrence'] !== null)  ? json_decode($request['workshopRefrence']) :[];
        $programTotal = $programs['total'];
        $newPrograms = [];
        foreach ($programs['data'] as $key => $program) {
            if ($program['workshop_id'] > 0) {
                if (!in_array($program['workshop_id'], $workshopRefrence)) {
                    $request->merge(['workshop_id' => $program['workshop_id']]);
                    $workshop = $this->programRepository->workshops($request->all());
                    
                    if ($workshop > 0) {
                        $newPrograms[$key] = $workshop[0];
                        $newPrograms[$key]['program_workshop_web_app'] = $workshop[0]['info']['name'];
                        $newPrograms[$key]['start_date_time'] = \Carbon\Carbon::parse($workshop[0]['date'] . ' ' . $workshop[0]['start_time'])->toDateTimeString();
                    }
                    $workshopRefrence[] = $program['workshop_id'];
                }
            }else{
                $newPrograms[]=$program;
            }
        }
        $programs['data'] = $newPrograms; 
        

        $programs = array_values(collect($programs['data'], $programs['workshop_id'])->sortBy('start_date_time')->all());

        //groups program by date
        $programs = collect($programs, "date")->groupBy('date')->all();

        

        $agenda_array = [];
        $agenda_by_group_array=[];
        if ($request['attendee_id']) {
            $attendee_agendas = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', '=', $request['attendee_id'])->whereNull('deleted_at')->get()->toArray();
            foreach ($attendee_agendas as $r) {
                $agenda_array[] = $r['agenda_id'];
            }
            $settings = \App\Models\AgendaSetting::where('event_id', '=', $event_id)->first();
            if ($settings['enable_program_attendee'] == 1) {
                $attendeeGroups = \App\Models\EventAttendeeGroup::where('attendee_id', '=', $request['attendee_id'])->whereHas('group', function ($query) {
                    return $query->where('status', '=', 1);
                })->with('group.assignAgendaGroups')->get()->toArray();
                foreach ($attendeeGroups as $attendeeGroup) {
                    foreach ($attendeeGroup['group']['assign_agenda_groups'] as $program) {
                        $agenda_by_group_array[] = $program['id'];
                    }
                }
            }
        }
        $program_setting = ProgramRepository::getSetting(["event_id" => $event_id]);
        $module_label = moduleNameFront($event_id, $language_id, 'agendas');
        $trackHtml = \View::make('web_app.program.track-detail', compact('subTracks','event_settings', 'event', 'track_name', 'track_color', 'track_id', 'labels', 'module_label', 'isFavorite' ))->render();
        $agendaHtml = \View::make('web_app.program.agenda-list', compact('programs', 'event', 'program_setting', 'request', 'favs', 'myturnlist', 'agenda_array', 'isTrack', 'prevDate', 'agenda_by_group_array'))->render();
        $prevDate = key(array_slice($programs, -1, 1, true));
        return response()->json([
            'success' => true,
            "data" => [
                'subTracks' => $subTracks,
                'programs' => $programs,
                'trackHtml' => $trackHtml,
                'agendaHtml' => $agendaHtml,
                'moduleName' => $module_label,
                'trackName' => $track_name,
                'programTotal' => $programTotal,
                'prevDate' => $prevDate,
                'workshopRefrence' => $workshopRefrence,
            ]
        ], $this->successStatus);
    }

    public function track_agendas_listing(Request $request, $event_id, $track_id)
    {
        $prevDate = isset($request["prevDate"]) && $request["prevDate"] !== ""  ? $request["prevDate"] : "";
        $favs = isset($request['fav']) && $request['fav'] === "yes" ? $request['fav'] : '';
        $isFavorite = isset($request['fav']) && $request['fav'] === "?fav=yes" ? '' : '';
        $event = \App\Models\Event::where('id', $event_id)->first()->toArray();
        $language_id = $event['language_id'];
        $event_settings     = \App\Models\EventSetting::select(['name', 'value'])->where('event_id', $event_id)->pluck('value', 'name')->toArray();
        $labels = eventsite_labels(['agendas', 'generallabels'], ['event_id' => $event['id'], 'language_id' => $event['language_id']]);
        $isTrack =  "&trackId=".$track_id;
        $request->merge([
            "event_id" => $event_id,
            "language_id" => $language_id,
            "track_id" => $track_id,
            "excludeSpeakerList" => true,
            "webAppListing" => true,
            "trackListing" => true,
            "limit"=> 40,
        ]);
        if ($favs === "yes") {
            $request->merge(["favs" => true]);
        }else{
            $request->merge(["hide_on_app" => true, "only_for_qa" => true]);
        }
        if (isset($formInput['searchText'])  && $formInput['searchText'] !== "") {
            $request->merge(["query" => $formInput['searchText']]);
        }
        $programs = $this->programRepository->search($request->all(), true);
        $total = $programs["total"];
        $workshopRefrence = (isset($request['workshopRefrence']) && $request['workshopRefrence'] !== null)  ? json_decode($request['workshopRefrence']) :[];
            $newPrograms = [];
            foreach ($programs['data'] as $key => $program) {
                if ($program['workshop_id'] > 0) {
                    if (!in_array($program['workshop_id'], $workshopRefrence)) {
                        $request->merge(['workshop_id' => $program['workshop_id']]);
                        $workshop = $this->programRepository->workshops($request->all());
                        
                        if ($workshop > 0) {
                            $newPrograms[$key] = $workshop[0];
                            $newPrograms[$key]['program_workshop_web_app'] = $workshop[0]['info']['name'];
                            $newPrograms[$key]['start_date_time'] = \Carbon\Carbon::parse($workshop[0]['date'] . ' ' . $workshop[0]['start_time'])->toDateTimeString();
                        }
                        $workshopRefrence[] = $program['workshop_id'];
                    }
                }else{
                    $newPrograms[]=$program;
                }
            }
            $programs['data'] = $newPrograms; 
        $programs = array_values(collect($programs['data'], $programs['workshop_id'])->sortBy('start_date_time')->all());

        //groups program by date
        $programs = collect($programs, "date")->groupBy('date')->all();

        $agenda_array = [];
        $agenda_by_group_array=[];
        if ($request['attendee_id']) {
            $attendee_agendas = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', '=', $request['attendee_id'])->whereNull('deleted_at')->get()->toArray();
            foreach ($attendee_agendas as $r) {
                $agenda_array[] = $r['agenda_id'];
            }
            $settings = \App\Models\AgendaSetting::where('event_id', '=', $event_id)->first();
            if ($settings['enable_program_attendee'] == 1) {
                $attendeeGroups = \App\Models\EventAttendeeGroup::where('attendee_id', '=', $request['attendee_id'])->whereHas('group', function ($query) {
                    return $query->where('status', '=', 1);
                })->with('group.assignAgendaGroups')->get()->toArray();
                foreach ($attendeeGroups as $attendeeGroup) {
                    foreach ($attendeeGroup['group']['assign_agenda_groups'] as $program) {
                        $agenda_by_group_array[] = $program['id'];
                    }
                }
            }
        }
        $program_setting = ProgramRepository::getSetting(["event_id" => $event_id]);
        $html = \View::make('web_app.program.agenda-list', compact('programs', 'event', 'program_setting', 'request', 'favs', 'myturnlist', 'agenda_array', 'isTrack', 'prevDate', "agenda_by_group_array"))->render();
        $prevDate = key(array_slice($programs, -1, 1, true));
        return response()->json([
            'success' => true,
            "data" => [
                'programs' => $programs,
                'total' => $total,
                'workshopRefrence' => $workshopRefrence,
                'prevDate' => $prevDate,
                'html' => $html,
            ]
        ], $this->successStatus);
    }

}
