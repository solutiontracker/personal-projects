<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\TemplateRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Http\Controllers\Wizard\Requests\Template\TemplateRequest;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\AttendeeRepository;

class TemplateController extends Controller
{
    public $successStatus = 200;

    protected $templateRepository;

    public function __construct(TemplateRepository $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function listing(Request $request)
    {
        $response = $this->templateRepository->listing($request->all());
        return response()->json([
            'success' => true,
            'data' => $response
        ], $this->successStatus);
    }

    public function update(TemplateRequest $request, $id)
    {
        $aliases = [
            "add_reg" => "registration_invite", "not_registered_invite" => "registration_invite", "not_registered_reminder" => "attendee_reminder_email", "app_invitation_sent" => "attendee", "app_invitation_not_sent" => "attendee"
        ];

        $event = $request->event;

        request()->merge([
            'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias(request()->event_id, 'attendee') : 0,
            'is_new_flow' => $event['registration_form_id'] === 1 ? 1 : 0
        ]);

        if (!is_numeric($id)) {

            request()->merge([
                "alias" => (isset($aliases[$id]) ? $aliases[$id] : ''),
            ]);

            $template = \App\Models\EventEmailTemplate::where('event_id', request()->event_id)->where('alias', '=', request()->alias)->where('type', '=', 'email')->first();

            if (!$template) {

                return response()->json([
                    'success' => true,
                    'message' => __('messages.not_found'),
                ], $this->successStatus);

            } else {
                $id = $template->id;
            }

        }

        if ($request->isMethod('PUT')) {

            $response = $this->templateRepository->edit(request()->all(), $id);

            EventRepository::add_module_progress(request()->all(), "template");

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);

        } else {

            $response = $this->templateRepository->getTemplateData(request()->all(), $id);

            return response()->json([
                'success' => true,
                'data' => $response,
            ], $this->successStatus);

        }
    }

    public function logs(Request $request, $template_id, $page = 1)
    {
        $request->merge(['page' =>  $page]);

        $request->merge(['template_id' =>  $template_id]);

        $response = $this->templateRepository->logs($request->all());
        return response()->json([
            'success' => true,
            'data' => $response
        ], $this->successStatus);
    }

    public function view_history(Request $request, $id)
    {
        $request->merge(['id' =>  $id]);

        $response = $this->templateRepository->view_history(request()->all());

        return response()->json([
            'success' => true,
            'data' => $response,
        ], $this->successStatus);
    }
}
