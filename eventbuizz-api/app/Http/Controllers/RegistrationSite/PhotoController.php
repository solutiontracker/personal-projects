<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\EventsitePhotoRepository;
use App\Http\Resources\EventsitePhoto as EventsitePhotoResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhotoController extends Controller
{
    public $successStatus = 200;

    protected $photoRepository;

    public function __construct(EventsitePhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    public function getEventsitePhotos(Request $request, $slug){

        $request->merge(['limit' =>  $request->limit ? $request->limit : 10]); 
        $photos = $this->photoRepository->getEventsitePhotos($request->all());
        return EventsitePhotoResource::collection($photos);
    }

}
