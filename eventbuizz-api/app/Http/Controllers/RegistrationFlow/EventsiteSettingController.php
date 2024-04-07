<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use App\Eventbuizz\Repositories\FormBuilderRepository;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Eventbuizz\Repositories\EventRepository;

use App\Eventbuizz\Repositories\EventSiteRepository;

use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;

class EventsiteSettingController extends Controller
{
    public $successStatus = 200;
    
    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @return void
     */
    public function eventsiteSectionFields(Request $request, $event_url)
    {
        $event = $request->event;

        $labels = $request->event['labels'];
        
        $event_calling_code = EventRepository::getEventCallingCode(['event_id' => $request->event_id]);

        $registration_form = EventSiteSettingRepository::getRegistrationForm(["event_id" => $request->event_id, 'type_id' => $request->attendee_type_id]);

        $registration_form_id = $registration_form ? $registration_form->id : 0;

        $sections = EventSiteSettingRepository::getAllSections(["event_id" => $request->event_id, "language_id" => $request->language_id, "status" => 1, 'registration_form_id' => $registration_form_id]);

        $payment_form_setting = EventSiteSettingRepository::getPaymentSetting(['registration_form_id' => $registration_form_id, "event_id" => $request->event_id])->toArray();

        $eventsite_form_setting = EventsiteRepository::getSetting(['registration_form_id' => $registration_form_id, "event_id" => $request->event_id])->toArray();;

        $request->merge([ "registration_form_id" => $registration_form_id ]);
                    
        $custom_fields = EventSiteRepository::getCustomFields($request->all());

        $form_builder_forms = FormBuilderRepository::getFormsStatic($request->event_id, $request->language_id, $registration_form_id);

        $attendee = array(
            'accept_foods_allergies' => $event['country_id'],
            'country' => $event['country_id'],
            'private_country' => $event['country_id'],
            'company_country' => $event['country_id'],
            'subscriber_ids' => [],
            'calling_code_phone' => $event_calling_code ? $event_calling_code : "+45",
            'registration_form_id' => $registration_form_id
        );

        //Stock 
        $stock_message = '';

        $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $request->event_id, 'status' => ['draft', 'completed']], false, true);

        //Validate stock
        $total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $attendee['registration_form_id']], true);

        $total = $total + 1;

        //Validate global stock
        $global_total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => 0], true);

        $global_total = $global_total + 1;

        $waiting_list_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => $request->event_id, 'registration_form_id' => $attendee['registration_form_id']]);
        
        if(!($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting['ticket_left'] > 0 && $total > (int)$eventsite_form_setting['ticket_left']) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left))))) {

            if((((int)$eventsite_form_setting['ticket_left'] > 0 && $total > (int)$eventsite_form_setting['ticket_left']) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left))) {
                $stock_message = $labels['REGISTER_TICKET_END'];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => array(
                "sections" => $sections,
                "attendee" => $attendee,
                "form_settings" =>  array_merge((array)$eventsite_form_setting, (array)$payment_form_setting),
                "custom_fields" => $custom_fields,
                "stock_message" => $stock_message,
                "form_builder_forms" => $form_builder_forms
            ),
        ], $this->successStatus);
    }
}
