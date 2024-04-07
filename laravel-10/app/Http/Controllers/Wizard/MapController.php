<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\MapRepository;
use App\Http\Controllers\Wizard\Requests\Map\MapRequest;

class MapController extends Controller
{
    protected $mapRepository;

    protected $successStatus = 200;

    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    public function store(MapRequest $request)
    {
        $response = $this->mapRepository->store($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
            'data' => (!empty($response)  ? $response : NULL)
        ], $this->successStatus);
    }

    public function update(MapRequest $request, $id)
    {
        $map = $this->mapRepository->getById($id);
        if ($map) {
            //validate request data
            $request->merge([
                'url' => ($request->google_map == 1 ? $request->url : ""),
                'image' => ($request->google_map == 0 ? $request->image : "")
            ]);

            $this->mapRepository->edit($request->all(), $map);

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('messages.not_exist'),
            ], $this->successStatus);
        }
    }

    public function fetch(Request $request)
    {
        $response = $this->mapRepository->fetch($request->event_id, $request->language_id);

        return response()->json([
            'success' => true,
            'message' => __('messages.fetch'),
            'data' => ($response ? $response : '')
        ], $this->successStatus);
    }
}
