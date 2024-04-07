<?php

namespace App\Http\Controllers\Wizard;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\EventRepository;
use App\Http\Controllers\Wizard\Requests\EventRequest;
use App\Http\Controllers\Wizard\Requests\Event\EventCloneRequest;
use App\Eventbuizz\Repositories\EventSettingRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Eventbuizz\Repositories\SubRegistrationRepository;
use App\Eventbuizz\Repositories\DirectoryRepository;
use Illuminate\Support\Arr;

class EventController extends Controller
{
    public $successStatus = 200;

    protected $eventRepository;

    protected $eventSettingRepository;

    protected $subRegistrationRepository;

    public function __construct(EventRepository $eventRepository, EventSettingRepository $eventSettingRepository, SubRegistrationRepository $subRegistrationRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->eventSettingRepository = $eventSettingRepository;
        $this->subRegistrationRepository = $subRegistrationRepository;
    }

    public function dashboard(Request $request)
    {
        $request->merge([
            'template_alias' => 'registration_invite'
        ]);

        $eventsite_setting =  \App\Models\EventsiteSetting::where('event_id', $request->event_id)->where('registration_form_id', 0)->first();

        $total_signups = AttendeeRepository::registered_attendees($request->event_id, true);

        $registered_invited_attendees = AttendeeRepository::registered_invited_attendees($request->all(), true);

        $cancelled_orders = EventsiteBillingOrderRepository::cancelledOrders($request->event_id, true);

        $totalOrders = EventsiteBillingOrderRepository::totalOrders($request->event_id, true);

        $waitingListOrders = EventsiteBillingOrderRepository::waitingListOrders($request->event_id, true);

        $not_attending_count = AttendeeRepository::not_attendees_list(['event_id' => $request->event_id, 'language_id' => $request->language_id], true);

        $attendee_invitation_stats = AttendeeRepository::attendee_invitation_stats($request->all());

        $not_registered = AttendeeRepository::not_registered($request->all(), true);

        $event_sub_registration_responses = $this->subRegistrationRepository->event_total_submissions($request->all());

        return response()->json([
            'success' => true,
            'data' => array(
                "signups" => AttendeeRepository::signup_stats($request->all()),
                "total_signups" => (int) $total_signups,
                "registered_invited_attendees" => (int) $registered_invited_attendees,
                "total_tickets" => (int) $eventsite_setting->ticket_left,
                "tickets_left" => (int) ($eventsite_setting->ticket_left >= $total_signups ? $eventsite_setting->ticket_left - $total_signups : 0),
                "days_remaining" => ($eventsite_setting->registration_end_date && !in_array($eventsite_setting->registration_end_date, ["0000-00-00 00:00:00", "1970-01-01 00:00:00"]) ? (\Carbon\Carbon::parse($eventsite_setting->registration_end_date)->greaterThanOrEqualTo(\Carbon\Carbon::now()) ? days(\Carbon\Carbon::now(), $eventsite_setting->registration_end_date) . ' ' . (days(\Carbon\Carbon::now(), $eventsite_setting->registration_end_date) > 1 ? trans('wizard.dashboard_analytics.days_remaining') : trans('wizard.dashboard_analytics.day_remaining')) : trans('wizard.dashboard_analytics.date_ended')) : ""),
                "invited" => AttendeeRepository::invited_attendees(['event_id' => $request->event_id, 'language_id' => $request->language_id], true),
                "not_attending" => (int) $not_attending_count,
                "response_rate" => ($not_attending_count + $registered_invited_attendees),
                "attendee_invitation_stats" => $attendee_invitation_stats,
                "cancelled_orders" => (int) $cancelled_orders,
                "totalOrders" => (int) $totalOrders,
                "waitingListOrders" => (int) $waitingListOrders,
                "not_registered" => (int) $not_registered,
                "event_sub_registration_responses" => (int) count($event_sub_registration_responses ?? []),
            )
        ], $this->successStatus);
    }

    public function listing(Request $request, $page)
    {
        $request->merge(['page' =>  $page]);
        $response = $this->eventRepository->listing($request->all());
        return response()->json([
            'success' => true,
            'data' => array(
                "result" => $response['data'],
                "total" => $response['total'],
                "current_page" => $response['current_page']
            )
        ], $this->successStatus);
    }

    public function store(EventRequest $request)
    {
        //validate request data
        $request->merge([
            'header_logo' => ($request->header_logo ? $request->header_logo : ""),
            'app_icon' => ($request->app_icon ? $request->app_icon : ""),
            'social_media_logo' => ($request->social_media_logo ? $request->social_media_logo : ""),
            'fav_icon' => ($request->fav_icon ? $request->fav_icon : ""),
            'eventsite_banners' => ($request->hasFile('eventsite_banners') ? $request->eventsite_banners : ""),
            'modules' => ($request->modules ? json_decode($request->modules, true) : ""),
            'menus' => ($request->menus ? json_decode($request->menus, true) : ""),
            'type' => 1,
            'start_date' => ($request->start_date ? \Carbon\Carbon::parse($request->start_date)->toDateString() : ''),
            'end_date' => ($request->end_date ? \Carbon\Carbon::parse($request->end_date)->toDateString() : ''),
            'start_time' => ($request->start_time ? \Carbon\Carbon::parse($request->start_time)->toTimeString() : ''),
            'end_time' => ($request->end_time ? \Carbon\Carbon::parse($request->end_time)->toTimeString() : ''),
            'third_party_redirect_url' => ($request->third_party_redirect_url ? $request->third_party_redirect_url : ''),
            'description' => ($request->description ? $request->description : ''),
            'cancellation_date' => ($request->cancellation_date ? \Carbon\Carbon::parse($request->cancellation_date)->toDateTimeString() : ''),
            'registration_end_date' => ($request->registration_end_date ? \Carbon\Carbon::parse($request->registration_end_date)->toDateTimeString() : ''),
            'registration_end_time' => ($request->registration_end_time ? \Carbon\Carbon::parse($request->registration_end_time)->toTimeString() : ''),
        ]);

        $event = $this->eventRepository->store($request->all());

        $event = $event->toArray();

        $default_template_id = \App\Models\EventEmailTemplate::where('event_id', $event['id'])->where('alias', 'registration_invite')->where('type', 'email')->value('id');

        $event['default_template_id'] = $default_template_id;

        $eventsite_setting = \App\Models\EventsiteSetting::where('event_id',  $event['id'])->where('registration_form_id', 0)->first();

        $event['registration_app_url'] = cdn('event/' . $event['url'] . '/detail');

        $event['mobile_app_url'] = cdn('event/' . $event['url']);

        if ($eventsite_setting->payment_type == 1) {
            $event['registration_form_url'] = cdn('event/' . $event['url'] . '/detail/registration#get_register');
        } else {
            $event['registration_form_url'] = cdn('event/' . $event['url'] . '/detail/free/registration#get_register');
        }

        //Eventsite setting
        $event['eventsite_setting'] = $eventsite_setting;

        //Eventsite payment setting
        $event['eventsite_payment_setting'] = \App\Models\EventsitePaymentSetting::where('event_id', $event['id'])->where('registration_form_id', 0)->first();

        //Eventsite secion fields
        $event['eventsite_secion_fields'] = EventSiteSettingRepository::getAllSectionFields(["event_id" => $event['id'], "language_id" => $event['language_id']]);

        //Fetch module settings
        $request->merge([
            'event_id' => $event['id'],
            'language_id' => $event['language_id'],
        ]);

        $modules = $this->eventSettingRepository->modules($request->all(), config('module.app_module_alias'), 'both');

        $module_permissions = array();

        foreach ($modules as $module) {
            $module_permissions[$module['alias']] = $module['status'];
        }

        $event['module_permissions'] = $module_permissions;

        $event['modules'] = $modules;

        $event['directory_sub_modules'] = DirectoryRepository::subModules($request->all());

        $event['defaultDirectory'] = Arr::first($event['directory_sub_modules']);

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
            'data' => array(
                "event" => $event
            )
        ], $this->successStatus);
    }

    public function update(EventRequest $request, $id)
    {
        //validate request data
        $request->merge([
            'header_logo' => ($request->header_logo ? $request->header_logo : ""),
            'app_icon' => ($request->app_icon ? $request->app_icon : ""),
            'social_media_logo' => ($request->social_media_logo ? $request->social_media_logo : ""),
            'fav_icon' => ($request->fav_icon ? $request->fav_icon : ""),
            'eventsite_banners' => ($request->hasFile('eventsite_banners') ? $request->eventsite_banners : ""),
            'start_date' => ($request->start_date ? \Carbon\Carbon::parse($request->start_date)->toDateString() : ''),
            'end_date' => ($request->end_date ? \Carbon\Carbon::parse($request->end_date)->toDateString() : ''),
            'start_time' => ($request->start_time ? \Carbon\Carbon::parse($request->start_time)->toTimeString() : ''),
            'end_time' => ($request->end_time ? \Carbon\Carbon::parse($request->end_time)->toTimeString() : ''),
            'cancellation_date' => ($request->cancellation_date ? \Carbon\Carbon::parse($request->cancellation_date)->toDateTimeString() : ''),
            'registration_end_date' => ($request->registration_end_date ? \Carbon\Carbon::parse($request->registration_end_date)->toDateTimeString() : ''),
            'registration_end_time' => ($request->registration_end_time ? \Carbon\Carbon::parse($request->registration_end_time)->toTimeString() : ''),
            'third_party_redirect_url' => ($request->third_party_redirect_url ? $request->third_party_redirect_url : ""),
            'description' => ($request->description ? $request->description : "")
        ]);

        $event = \App\Models\Event::find($id);

        if ($event) {

            if (\Carbon\Carbon::parse(\Carbon\Carbon::now())->greaterThanOrEqualTo($event->start_date) && \Carbon\Carbon::parse($request->start_date)->notEqualTo(\Carbon\Carbon::parse($event->start_date))) {
                
                return response()->json([
                    'success' => false,
                    'message' => __('messages.on_event_continue'),
                    'errors' => []
                ], $this->successStatus);

            } else {

                $event = $this->eventRepository->edit($request->except(['language_id', 'languages_id', 'from_event_id']),  $event);

                $event = $event->toArray();

                $default_template_id = \App\Models\EventEmailTemplate::where('event_id', $event['id'])->where('alias', 'registration_invite')->where('type', 'email')->value('id');

                $event['default_template_id'] = $default_template_id;

                $eventsite_setting = \App\Models\EventsiteSetting::where('event_id',  $event['id'])->where('registration_form_id', 0)->first();

                $event['registration_app_url'] = cdn('event/' . $event['url'] . '/detail');

                $event['mobile_app_url'] = cdn('event/' . $event['url']);

                if ($eventsite_setting->payment_type == 1) {
                    $event['registration_form_url'] = cdn('event/' . $event['url'] . '/detail/registration#get_register');
                } else {
                    $event['registration_form_url'] = cdn('event/' . $event['url'] . '/detail/free/registration#get_register');
                }

                //Eventsite setting
                $event['eventsite_setting'] = $eventsite_setting;

                //Eventsite payment setting
                $event['eventsite_payment_setting'] = \App\Models\EventsitePaymentSetting::where('event_id', $event['id'])->where('registration_form_id', 0)->first();

                //Eventsite secion fields
                $event['eventsite_secion_fields'] = EventSiteSettingRepository::getAllSectionFields(["event_id" => $event['id'], "language_id" => $event['language_id']]);

                $modules = $this->eventSettingRepository->modules($request->all(), config('module.app_module_alias'), 'both');

                $module_permissions = array();

                foreach ($modules as $module) {
                    $module_permissions[$module['alias']] = $module['status'];
                }

                $event['module_permissions'] = $module_permissions;

                $event['modules'] = $modules;

                $event['directory_sub_modules'] = DirectoryRepository::subModules($request->all());

                $event['defaultDirectory'] = Arr::first($event['directory_sub_modules']);

                return response()->json([
                    'success' => true,
                    'message' => __('messages.update'),
                    'data' => array(
                        "event" => $event
                    )
                ], $this->successStatus);
            }

        } else {

            return response()->json([
                'success' => true,
                'message' => __('messages.not_found'),
            ], $this->successStatus);

        }
    }

    public function destroy(Request $request, $id)
    {
        $this->eventRepository->destroy($id);
        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function fetchEvent(Request $request, $id)
    {

        $event = $request->event;

        if(!$event) $event = \App\Models\Event::where('id', $id)->first();

        $request->merge(['registration_form_id' => $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($id, 'attendee') : 0]);
        
        $response = $this->eventRepository->fetchEvent($request->all(), $id);

        $request->merge([
            'event_id' => $response['event']['id'],
            'language_id' => $response['event']['language_id'],
        ]);

        $modules = $this->eventSettingRepository->modules($request->all(), config('module.app_module_alias'), 'both');

        $module_permissions = array();

        foreach ($modules as $module) {
            $module_permissions[$module['alias']] = $module['status'];
        }

        $response['event']['modules'] = $modules;

        $response['event']['module_permissions'] = $module_permissions;

        $response['event']['directory_sub_modules'] = DirectoryRepository::subModules($request->all());

        $response['event']['defaultDirectory'] = Arr::first($response['event']['directory_sub_modules']);

        return response()->json([
            'success' => true,
            'data' => array(
                "detail" => (!empty($response) ? $response : null),
                "module_permissions" => $module_permissions,
                "modules" => $modules
            )
        ], $this->successStatus);

    }

    public function templates(Request $request)
    {
        $response = $this->eventRepository->templates($request->all());

        $selectedTemplate = array_filter($response, function ($template) use ($request) {
            return $request->from_event_id == $template['id'];
        });

        return response()->json([
            'success' => true,
            'data' => array(
                "results" => $response,
                "selectedTemplate" => (count($selectedTemplate ?? []) > 0 ? array_shift($selectedTemplate) : [])
            )
        ], $this->successStatus);
        
    }

    public function progress(Request $request)
    {
        $response = $this->eventRepository->fetch_module_progress($request->all());
        return response()->json([
            'success' => true,
            'data' => array(
                "modules" => $response
            )
        ], $this->successStatus);
    }

    public function copy(EventCloneRequest $request, $id)
    {
        $request->merge(["from_event_id" => $id]);

        $event = $this->eventRepository->copy($request->all());

        $request->merge([
            'event_id' => $event->id,
            'language_id' => $event->language_id,
        ]);

        $modules = $this->eventSettingRepository->modules($request->all(), config('module.app_module_alias'), 'both');

        $module_permissions = array();

        foreach ($modules as $module) {
            $module_permissions[$module['alias']] = $module['status'];
        }

        $event->modules = $modules;

        $event->module_permissions = $module_permissions;

        return response()->json([
            'success' => true,
            'event' => $event,
            'message' => __('messages.create'),
        ], $this->successStatus);
    }

    public function getSubAdmins(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // If event is not created by current user then return 503 response.
        if($request->user()->parent_id !== 0 && ($request->user()->id !== $event->owner_id)){
            return response()->json(['success' => false, 'message' => 'you are not allowed'], 503);
        }

        $ass_admins = $this->eventRepository->getAssignedAdmins($request->all(), $id);
        $unass_admins = $this->eventRepository->getUnassignedAdmins($request->all(), $id);

        return response()->json([
            'success' => true,
            'data' => array(
                'assigned' => $ass_admins,
                'unassigned' => $unass_admins
            ),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function UnassignAdmins(Request $request, $id)
    {
        $this->eventRepository->unassignAdmin($request->all(), $id);

        return response()->json([
            'success' => true,
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignAdmins(Request $request, $id)
    {
        $this->eventRepository->assignAdmin($request->all(), $id);

        return response()->json([
            'success' => true,
        ], $this->successStatus);
    }
}
