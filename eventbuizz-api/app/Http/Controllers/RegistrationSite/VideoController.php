<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventsiteVideoRepository;
use App\Http\Resources\EventsiteVideo as EventsiteVideoResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VideoController extends Controller
{
    public $successStatus = 200;

    protected $videoRepository;

    public function __construct(EventsiteVideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function getEventsiteVideos(Request $request, $slug){

        $request->merge(['limit' =>  $request->limit ? $request->limit : 10]); 
        $videos = $this->videoRepository->getEventsiteVideos($request->all());

        return EventsiteVideoResource::collection($videos);
    }

}
