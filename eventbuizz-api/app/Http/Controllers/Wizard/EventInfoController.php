<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventInfoRepository;
use App\Http\Controllers\Wizard\Requests\EventInfo\LinkRequest;
use App\Http\Controllers\Wizard\Requests\EventInfo\PageRequest;
use App\Http\Controllers\Wizard\Requests\EventInfo\MenuRequest;
use App\Eventbuizz\Repositories\InformationPagesRepository;

class EventInfoController extends Controller
{
    public $successStatus = 200;

    protected $eventInfoRepository, $informationPagesRepository;

    public function __construct(EventInfoRepository $eventInfoRepository, InformationPagesRepository $eventInfoPagesRepository)
    {
        $this->eventInfoRepository = $eventInfoRepository;
        $this->informationPagesRepository = $eventInfoPagesRepository;
    }

    public function listing(Request $request, $cms, $id = 0)
    {
        $response = array();

        $response = $this->hierarchy($response, $request->all(), $cms, $id);

        return response()->json([
            'success' => true,
            'data' => $response
        ], $this->successStatus);
    }

    public function hierarchy($response, $request, $cms, $parent_id)
    {
        if($cms === "information-pages"){
            $result = $this->informationPagesRepository->listing($request, $parent_id);
        }else{
            $result = $this->eventInfoRepository->listing($request, $cms, $parent_id);
        }

        foreach ($result as $key => $row) {
            $response[$key] = $row;
            if ($row['type'] == "folder") {
                $response[$key]['subItems'] = $this->hierarchy([], $request, $cms, $row['id']);
            }
        }
        return $response;
    }

    public function store(MenuRequest $menu_request, LinkRequest $link_request, PageRequest $page_request, $cms, $type)
    {
        request()->merge(['has_image' => (request()->hasFile('image') ? 1 : 0)]);
        request()->merge(['has_pdf' => (request()->hasFile('pdf') ? 1 : 0)]);
        request()->merge(['include_image' => 1]);
        request()->merge(['include_pdf' => 1]);
        request()->merge(['show_in_app' => request()->input("showInApp")]);
        request()->merge(["show_in_reg_site" => request()->input("showInWebsite")]);

        $response = $this->eventInfoRepository->store(request()->all(), $type, $cms);

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
            'response' => $response,
        ], $this->successStatus);
    }

    public function update(MenuRequest $menu_request, LinkRequest $link_request, PageRequest $page_request, $cms, $type, $id)
    {
        request()->merge(['has_image' => (request()->hasFile('image') ? 1 : 0)]);
        request()->merge(['has_pdf' => (request()->hasFile('pdf') ? 1 : 0)]);
        request()->merge(['include_image' => 1]);
        request()->merge(['include_pdf' => 1]);
        request()->merge(['show_in_app' => request()->input("showInApp")]);
        request()->merge(["show_in_reg_site" => request()->input("showInWebsite")]);

        $response = $this->eventInfoRepository->edit(request()->all(), $type, $cms, $id);

        return response()->json([
            'success' => $response['status'],
            'message' => $response['message'],
        ], $this->successStatus);
    }

    public function destroy(Request $request, $cms, $type, $id)
    {
        $this->eventInfoRepository->destroy($type, $cms, $id);

        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function update_order(Request $request, $cms) {
        $this->eventInfoRepository->updateOrder($cms, $request->list);

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }
}
