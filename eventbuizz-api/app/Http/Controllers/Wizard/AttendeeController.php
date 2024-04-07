<?php

namespace App\Http\Controllers\Wizard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Http\Controllers\Wizard\Requests\AttendeeRequest;
use App\Eventbuizz\Repositories\ImportRepository;
use App\Eventbuizz\Repositories\LabelRepository;
use App\Eventbuizz\Repositories\EventRepository;
use Illuminate\Support\Arr;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;

class AttendeeController extends Controller
{
    protected $attendeeRepository;

    protected $importRepository;

    protected $labelRepository;

    public $successStatus = 200;

    public function __construct(AttendeeRepository $attendeeRepository, ImportRepository $importRepository, LabelRepository $labelRepository)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->importRepository = $importRepository;
        $this->labelRepository = $labelRepository;
    }

    public function listing(Request $request, $page)
    {

        $request->merge(['page' =>  $page]);

        if($request->has('wizard_listing') || ($request->has('type')&& $request->get('type')=='unassigned-speakers')){
            $response = $this->attendeeRepository->wizardListing($request->all());
        }else{
            $response = $this->attendeeRepository->listing($request->all());
        }

        $attendee_types = AttendeeRepository::getAttendeeTypes($request->event_id, $request->language_id);

        $registered_attendees = AttendeeRepository::registered_attendees($request->event_id, true);

        return response()->json([
            'success' => true,
            'data' => $response,
            'attendee_types' => $attendee_types,
            'registered_attendees' => $registered_attendees,
        ], $this->successStatus);
    }

    public function store(AttendeeRequest $request)
    {
        $this->attendeeRepository->store($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
        ], $this->successStatus);
    }

    public function update(AttendeeRequest $request, $id)
    {
        $attendee = $this->attendeeRepository->getById($id);
        if ($attendee) {
            $this->attendeeRepository->edit($request->all(), $attendee);

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

    public function destroy(Request $request, $id)
    {
        $event_setting = EventSiteSettingRepository::getSetting($request->all());
        if ($id == "selected" || $id == "all") {
            if ($id == "selected") {
                $ids = $request->ids;
            } else if ($id == "all") {
                $query = \App\Models\Event::find($request->event_id)->attendees();
                if ($request->speaker == 1) $query->where('speaker', 1);
                $ids = $query->pluck('conf_attendees.id')->toArray();
            }

            $response = array();

            foreach ($ids as $id) {
                if($event_setting->payment_type == 0) {
                    $this->attendeeRepository->unAssign($request->all(), $id);
                } else {
                    $response[] = $this->attendeeRepository->destroy($request->all(), $id);
                }  
            }

            $success = array_filter($response, function ($row) {
                return $row['status'] == true;
            });

            $errors = array_filter($response, function ($row) {
                return $row['status'] == false;
            });

            if(count($success) == 0 && count($errors) > 1) {
                $order_ids = Arr::pluck($errors, 'order_id');
                return response()->json([
                    'success' => false,
                    'message' => sprintf(__('messages.on_attendees_delete_error'), implode(', ', $order_ids), implode(', ', $order_ids)),
                ], $this->successStatus);
            } else if(count($success) == 0 && count($errors) == 1) {
                return response()->json([
                    'success' => $response[0]['status'],
                    'message' => $response[0]['message'],
                ], $this->successStatus);
            } else if(count($success) > 0 && count($errors) > 0) {
                $order_ids = Arr::pluck($errors, 'order_id');
                return response()->json([
                    'success' => true,
                    'message' => sprintf(__('messages.on_attendees_delete_success'), implode(', ', $order_ids)),
                ], $this->successStatus);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.delete'),
                ], $this->successStatus);
            }

            return response()->json([
                'success' => $response['status'],
                'message' => $response['message'],
            ], $this->successStatus);
        } else {
            
            if($event_setting->payment_type == 0) {
                $response = $this->attendeeRepository->unAssign($request->all(), $id);
            } else {
                $response = $this->attendeeRepository->destroy($request->all(), $id);
            }
            
            return response()->json([
                'success' => $response['status'],
                'message' => $response['message'],
            ], $this->successStatus);
        }
    }

    public function invitations(Request $request, $page)
    {
        if ($request->isMethod('POST')) {
            $request->merge(['page' =>  $page]);
            $response = $this->attendeeRepository->invitations($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }

    public function save_invitation(AttendeeRequest $request, $id = null)
    {
        if ($request->isMethod('POST')) {
            $this->attendeeRepository->create_invitation($request->all());

            return response()->json([
                'success' => true,
                'message' => __('messages.create'),
            ], $this->successStatus);
        } else if ($request->isMethod('PUT')) {
            $this->attendeeRepository->update_invitation($request->all(), $id);

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);
        }
    }

    public function destroy_invitation(Request $request, $id = null)
    {
        $request->merge(['module' => (in_array($request->module, ["not_registered_invite", "not_registered_reminder"]) ? "not_registered" : $request->module)]);

        $id = ($id == "selected" ? $request->ids : $id);

        $this->attendeeRepository->destroy_invitation($request->all(), $id);

        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    public function invitation_process(Request $request)
    {
        $request->merge(['module' => (in_array($request->module, ["not_registered_invite", "not_registered_reminder"]) ? "not_registered" : $request->module)]);

        if ($request->action == "invitation_delete") {
            $this->attendeeRepository->destroy_invitation($request->all(), $request->ids);

            return response()->json([
                'success' => true,
                'redirect' => true
            ], $this->successStatus);
        } else if ($request->action == "invite_send_only") {
            $this->attendeeRepository->update_invitation_status($request->all());

            return response()->json([
                'success' => true,
                'redirect' => true
            ], $this->successStatus);
        } else if ($request->action == "move_registration_to_not_attendee_input") {
            $this->attendeeRepository->move_not_registered_to_not_attending($request->all(), $request->ids);

            return response()->json([
                'success' => true,
                'redirect' => true
            ], $this->successStatus);
        } else {
            $response = $this->attendeeRepository->invite_attendees($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }
    
    /**
     * invitation_template
     *
     * @param  mixed $request
     * @return void
     */
    public function invitation_template(Request $request)
    {
        $event = $request->event;

        $request->merge(['module' => (in_array($request->module, ["not_registered_invite", "not_registered_reminder"]) ? "not_registered" : $request->module), 'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

        $response = $this->attendeeRepository->invitation_template($request->all());

        return response()->json([
            'success' => true,
            'data' => $response
        ], $this->successStatus);
    }
    
    /**
     * send_invitation
     *
     * @param  mixed $request
     * @return void
     */
    public function send_invitation(Request $request)
    {
        $event = $request->event;
        
        $request->merge(['module' => (in_array($request->module, ["not_registered_invite", "not_registered_reminder"]) ? "not_registered" : $request->module), 'registration_form_id'=> $event['registration_form_id'] === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($request->event_id, 'attendee') : 0]);

        $response = $this->attendeeRepository->send_invitation($request->all());

        if ($request->module) EventRepository::add_module_progress($request->all(), "invitation_" . $request->module);

        return response()->json([
            'success' => true,
            'message' => __('messages.invitation_send_message'),
            'data' => array(
                "ids" => $response['ids']
            )
        ], $this->successStatus);
    }

    public function app_invitations(Request $request, $page)
    {
        if ($request->isMethod('POST')) {
            $request->merge(['page' =>  $page]);

            $response = $this->attendeeRepository->app_invitations($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }

    public function app_invitations_not_sent(Request $request, $page)
    {
        if ($request->isMethod('POST')) {
            $request->merge(['page' =>  $page]);

            $response = $this->attendeeRepository->app_invitations_not_sent($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }

    public function not_registered(Request $request, $page)
    {
        if ($request->isMethod('POST')) {
            $request->merge(['page' =>  $page]);

            $response = $this->attendeeRepository->not_registered($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }

    public function not_attendees_list(Request $request, $page)
    {
        if ($request->isMethod('POST')) {
            $request->merge(['page' =>  $page]);

            $response = $this->attendeeRepository->not_attendees_list($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }

    public function registration_invitations_reminder_log(Request $request, $page)
    {
        if ($request->isMethod('POST')) {
            $request->merge(['page' =>  $page]);

            $response = $this->attendeeRepository->registration_invitations_reminder_log($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }

    public function app_invitations_reminder_log(Request $request, $page)
    {
        if ($request->isMethod('POST')) {
            $request->merge(['page' =>  $page]);

            $response = $this->attendeeRepository->app_invitations_reminder_log($request->all());

            return response()->json([
                'success' => true,
                'data' => $response
            ], $this->successStatus);
        }
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('GET')) {
            $response = $this->attendeeRepository->getAttendeeSetting($request->event_id);

            return response()->json([
                'success' => true,
                'data' => [
                    "settings" => $response
                ]
            ], $this->successStatus);
        } else if ($request->isMethod('PUT')) {
            $response = $this->attendeeRepository->updateAttendeeSetting($request->only(['event_id', 'attendee_reg_verification', 'validate_attendee_invite']));

            return response()->json([
                'success' => true,
                'message' => __('messages.update'),
            ], $this->successStatus);
        }
    }

    public function export_invitations(Request $request, $type)
    {
        if ($request->isMethod('GET')) {
            if ($type == "registration-invitations") {
                $records = $this->attendeeRepository->getExportRegistrationInvites($request->all());
                $settings = $this->attendeeRepository->getImportSettingsInvites(true);
            } else if ($type == "not-registered") {
                $records = $this->attendeeRepository->getNotRegisteredAttendeesExport($request->all());
                $settings = $this->attendeeRepository->getImportSettingsNotRegistered(true);
            } else if ($type == "not-attending-list") {
                $records = $this->attendeeRepository->getNotAttendingListExport($request->all());
                $settings = $this->attendeeRepository->getImportNotAttendingSettings(true);
            }

            $header_data = array();
            foreach ($settings['fields'] as $headers) {
                $header_data[] = $headers['label'];
            }

            array_unshift($records, $header_data);

            $file_name = $this->labelRepository->getEventLabels($request->all(), 'exportlabels', 'EXPORT_INVITE_ATTENDEES');

            $filename = $file_name . time() . '.csv';

            $this->importRepository->export($request->all(), $records, $filename, '', false);
        }
    }

    public function send_test_email(Request $request)
    {
        $this->attendeeRepository->send_test_email($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Your test email has been sent successfully.'
        ], $this->successStatus);
    }

    public function invitations_stats(Request $request)
    {
        $add_reg = $this->attendeeRepository->invitations($request->all(), true);
        $not_registered = $this->attendeeRepository->not_registered($request->all(), true);
        $app_invitations_not_sent = $this->attendeeRepository->app_invitations_not_sent($request->all(), true);
        $app_invitations = $this->attendeeRepository->app_invitations($request->all(), true);

        return response()->json([
            'success' => true,
            'data' => array(
                "add_reg" => $add_reg,
                "not_registered" => $not_registered,
                "app_invitations_not_sent" => $app_invitations_not_sent,
                "app_invitations" => $app_invitations,
            )
        ], $this->successStatus);
    }

    public function attendee_type(AttendeeRequest $request)
    {
        if ($request->isMethod('POST')) {
            $this->attendeeRepository->store_attendee_type($request->all());

            return response()->json([
                'success' => true,
                'message' => __('messages.create'),
            ], $this->successStatus);
        }
    }

    public function export(Request $request)
    {
        if ($request->isMethod('GET')) {
            $settings = $this->attendeeRepository->getExportSettingWithSubReg($request->all());
            $records = $this->attendeeRepository->exportAssignAttendees($request->all());
            $temp_setting['attendee_id'] = array('field' => 'attendee_id', 'label' => 'Attendee ID', 'type' => 'string', 'required' => true);

            $settings['fields'] = array_merge($temp_setting, $settings['fields']);
            $header_data = array();
            foreach ($settings['fields'] as $headers) {
                $header_data[] = $headers['label'];
            }
            array_unshift($records, $header_data);
            $file_name = $this->labelRepository->getEventLabels($request->all(), 'exportlabels', 'EXPORT_ATTENDEES_ALL');
            $filename = $file_name . time() . '.csv';
            $this->importRepository->export($request->all(), $records, $filename, '', false);
        }
    }

}
