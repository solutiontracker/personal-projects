<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\MapRepository;
use App\Http\Resources\Map as MapResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MapController extends Controller
{
    public $successStatus = 200;

    protected $mapRepository;

    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    public function getMap(Request $request, $slug)
    {
        $map = $this->mapRepository->getMap($request->all());

        return new MapResource($map);
    }
}
