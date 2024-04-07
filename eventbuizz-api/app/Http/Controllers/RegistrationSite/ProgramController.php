<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\ProgramRepository;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Http\Resources\EventAgenda as EventAgendaResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ProgramController extends Controller
{
    public $successStatus = 200;

    protected $programRepository;
    
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
    
    /**
     * getPrograms
     *
     * @param  mixed $request
     * @return void
     */
    public function getPrograms(Request $request)
    {
        $programs = array();

        $tracks = $this->programRepository->getAllTracks($request->all());

        foreach ($tracks as $key => $track) {
            foreach ($track['info'] as $info) {
                $track[$info['name']] = $info['value'];
            }
            unset($track['info']);
            $tracks[$key]=$track;
        }

        $request->merge(['webAppListing'=> true ]); 

        $data = $this->programRepository->search($request->all(), false);

        //groups program by date
        $data['data'] = collect($data['data'], "date")->groupBy('date')->all();

        $skip_workshops = array();
        
        foreach($data['data'] as $date => $program) {
            foreach ($program as $key => $row) {
                if ($row['workshop_id'] > 0) {
                    if (!in_array($row['workshop_id'], $skip_workshops)) {
                        $request->merge(['workshop_id' => $row['workshop_id']]);
                        $workshop_programs = ProgramRepository::workshopPrograms($request->all());
                        $row['workshop_programs'] = $workshop_programs;
                        $programs[$date][] = $row;
                        $skip_workshops[] = $row['workshop_id'];
                    }
                }else{
                    $programs[$date][]=$row;
                }
            }
        }


        return response()->json([
            'success' => true,
            "data" =>  [
                'programs' => $programs,
                'tracks' => $tracks,
            ],
        ], $this->successStatus);

    }
    
    /**
     * Program advance search
     *
     * @param  mixed $request
     * @return void
     */
    public function search(Request $request)
    {
        $programs = array();

        $event = $request->event;

        //Program setting 
        $program_setting = ProgramRepository::getSetting($request->all());

        //Speaker setting
        $speaker_setting = AttendeeRepository::getSpeakerSetting($request->all());

        $request->merge([
            'limit' =>  $request->limit ? $request->limit : 1000,
            'speaker_setting' => $speaker_setting,
            'hide_on_registrationsite' => true,
            'only_for_qa' => true,
            'excludeSpeakerList' => true,
        ]); 

        $workshops = $this->programRepository->workshops($request->all());

        $programs = $this->programRepository->search($request->all(), true);

        $i = $programs['total'];

        foreach($workshops as $workshop) {
            $request->merge(['workshop_id' => $workshop['id']]);
            $workshop_programs = $this->programRepository->search($request->all(), true);
            if($workshop_programs['total'] > 0) {
                $programs['data'][$i] = $workshop;
                $programs['data'][$i]['program_workshop'] = $workshop['info']['name'];
                $programs['data'][$i]['workshop_programs'] = $workshop_programs;
                $programs['data'][$i]['start_date_time'] = \Carbon\Carbon::parse($workshop['date'].' '.$workshop['start_time'])->toDateTimeString();
                $i++;
            } 
        }

        $programs = array_values(collect($programs['data'])->sortBy('start_date_time')->all());

        //groups program by date
        $programs = collect($programs, "date")->groupBy('date')->all();

        $input = $request->all();
        $html = \View::make('registration_site.programs.program-search-listing', compact('programs', 'event', 'program_setting', 'speaker_setting', 'input'))->render();

        return response()->json([
            'success' => true,
            "data" => [
                'programs' => $programs,
                'html' => $html,
            ]
        ], $this->successStatus);
    }

    /**
     * getProgram
     * @param  mixed $request
     * @return void
     */
    public function getProgram(Request $request, $event_url, $id)
    {
        $input = $request->all();

        $event = $request->event;

        $request->merge(["program_id" => $id]);

        $program = $this->programRepository->getProgramDetail($request->all());

        //Program setting 
        $program_setting = ProgramRepository::getSetting($request->all());

        //Speaker setting
        $speaker_setting = AttendeeRepository::getSpeakerSetting($request->all());
        
        $html = \View::make('registration_site.programs.program-detail-popup', compact('program', 'event', 'program_setting', 'speaker_setting', 'input'))->render();

        return response()->json([
            'success' => true,
            "data" => [
                'program' => $program,
                'html' => $html,
            ]
        ], $this->successStatus);
    }

    
    /**
     * getPrograms
     *
     * @param  mixed $request
     * @return void
     */
    public function getAttendeePrograms(Request $request)
    {
        $programs = array();
		$id = $request->user()->id;
        $request->merge(['favs'=>true, 'attendee_id' => $id]); 

        $data = $this->programRepository->search($request->all(), false);

        //groups program by date
        $data['data'] = collect($data['data'], "date")->groupBy('date')->all();

        $skip_workshops = array();
    
		foreach($data['data'] as $date => $program) {
            foreach ($program as $key => $row) {
                if ($row['workshop_id'] > 0) {
                    if (!in_array($row['workshop_id'], $skip_workshops)) {
						$formInput['workshop_id'] = $row['workshop_id'];
						$formInput['attendee_id'] = $id;
						$formInput['favs'] = true;
                        $workshop_programs = ProgramRepository::workshopPrograms($formInput);
                        $row['workshop_programs'] = $workshop_programs;
                        $programs[$date][] = $row;
                        $skip_workshops[] = $row['workshop_id'];
                    }
                }else{
                    $programs[$date][]=$row;
                }
            }
        }

        return response()->json([
            'success' => true,
            "data" =>  $programs,
        ], $this->successStatus);

    }

}
