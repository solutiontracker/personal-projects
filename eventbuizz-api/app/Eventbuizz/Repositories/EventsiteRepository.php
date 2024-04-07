<?php

namespace App\Eventbuizz\Repositories;

use App\Models\BillingItem;
use Illuminate\Http\Request;

class EventsiteRepository extends AbstractRepository
{
	protected $eventsite_setting_fields = ['ticket_left', 'registration_end_date', 'registration_end_time', 'cancellation_date', 'cancellation_end_time', 'registration_code', 'mobile_phone', 'eventsite_public', 'eventsite_signup_linkedin', 'eventsite_signup_fb', 'eventsite_tickets_left', 'eventsite_time_left', 'eventsite_language_menu', 'eventsite_menu', 'eventsite_banners', 'eventsite_location', 'eventsite_date', 'eventsite_footer', 'pass_changeable', 'phone_mandatory', 'attendee_registration_invite_email', 'attach_attendee_ticket', 'attendee_my_profile', 'attendee_my_program', 'attendee_my_billing', 'attendee_my_billing_history', 'attendee_my_reg_cancel', 'attendee_my_sub_registration', 'attendee_go_to_mbl_app', 'goto_eventsite', 'goto_eventsite', 'payment_type', 'use_waitinglist', 'eventsite_add_calender', 'auto_complete', 'registration_after_login', 'new_message_temp', 'third_party_redirect', 'third_party_redirect_url', 'attach_my_program', 'quick_register', 'prefill_reg_form'];

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * settings / when new event create / cloning event
	 *
	 * @param array
	 */
	public function install($request)
	{

		$settings = \App\Models\EventsitePaymentSetting::where('event_id', $request['from_event_id'])->get();

		//Delete old settings and copy new one
		\App\Models\EventsitePaymentSetting::where('event_id', $request['to_event_id'])->delete();

		if (count($settings)) {
			foreach ($settings as $record) {
				$record = $record->replicate();
				$record->event_id = $request['to_event_id'];
				if (session()->has('clone.event.event_registration_form.' . $record->registration_form_id) && $record->registration_form_id > 0) {
					$record->registration_form_id = session()->get('clone.event.event_registration_form.' . $record->registration_form_id);
				}
				$record->save();
			}
		} else {
			\App\Models\EventsitePaymentSetting::create([
				"billing_item_type" => 0,
				"eventsite_currency" => 208,
				"billing_type" => 0,
				"dimension_h" => 100,
				"dimension_w" => 100,
				"event_id" => $request['to_event_id'],
			]);
		}

		//Custom fields
		$parent_custom_fields = \App\Models\EventCustomField::where('event_id', $request['from_event_id'])
			->where('parent_id', 0)
			->get();

		foreach($parent_custom_fields as $parent_custom_field) {

			$to_parent_custom_field = $parent_custom_field->replicate();

			$to_parent_custom_field->event_id = $request['to_event_id'];

			if (session()->has('clone.event.event_registration_form.' . $parent_custom_field->registration_form_id) && $parent_custom_field->registration_form_id > 0) {
				$to_parent_custom_field->registration_form_id = session()->get('clone.event.event_registration_form.' . $parent_custom_field->registration_form_id);
			}

            $to_parent_custom_field->save();

			//Info
			$from_parent_custom_field_infos = \App\Models\EventCustomFieldInfo::where('custom_field_id', $parent_custom_field->id)->get();
			
			foreach($from_parent_custom_field_infos as $info) {
				$to_parent_custom_field_info = $info->replicate();
				$to_parent_custom_field_info->custom_field_id = $to_parent_custom_field->id;
				$to_parent_custom_field_info->save();
			}

			$child_custom_fields = \App\Models\EventCustomField::where('event_id', $request['from_event_id'])
			->where('parent_id', $parent_custom_field->id)
			->get();
			
			foreach($child_custom_fields as $child_custom_field) {

				$to_child_custom_field = $child_custom_field->replicate();
				
				$to_child_custom_field->event_id = $request['to_event_id'];

				$to_child_custom_field->parent_id = $to_parent_custom_field->id;

				if (session()->has('clone.event.event_registration_form.' . $child_custom_field->registration_form_id) && $child_custom_field->registration_form_id > 0) {
					$to_child_custom_field->registration_form_id = session()->get('clone.event.event_registration_form.' . $child_custom_field->registration_form_id);
				}
				
				$to_child_custom_field->save();

				//Info
				$from_child_custom_field_infos = \App\Models\EventCustomFieldInfo::where('custom_field_id', $child_custom_field->id)->get();
				
				foreach($from_child_custom_field_infos as $info) {
					$to_child_custom_field_info = $info->replicate();
					$to_child_custom_field_info->custom_field_id = $to_child_custom_field->id;
					$to_child_custom_field_info->save();
				}
			}
		}
		
	}

	/**
	 * save eventsite settings
	 *
	 * @param int
	 * @param array
	 * @param string
	 */
	public function saveSetting($event_id, $formInput, $default)
	{
		$settings = \App\Models\EventsiteSetting::where('event_id', '=', $event_id)->where('registration_form_id', 0)->first();

		if ($settings) {

			foreach ($this->eventsite_setting_fields as $key => $value) {

				if (array_key_exists($value, $formInput) && !is_array($formInput[$value])) {

					if ($value == 'registration_end_date' && $formInput[$value]) $formInput[$value] = date('Y-m-d', strtotime($formInput[$value]));

					if ($value == 'cancellation_date' && $formInput[$value]) $formInput[$value] = date('Y-m-d', strtotime($formInput[$value]));

					if ($value == 'payment_type') {
						\App\Models\EventsitePaymentSetting::where('event_id', '=', $event_id)->where('registration_form_id', 0)->update(array('eventsite_billing' => $formInput[$value]));
					}

					$settings->$value = addslashes($formInput[$value]);
				}

			}

			if (isset($formInput['send_invoice_email'])) $settings->send_invoice_email = $formInput['send_invoice_email'];

			if (isset($formInput['attach_invoice_email']))  $settings->attach_invoice_email = $formInput['attach_invoice_email'];

			if (isset($formInput['agenda_search_filter']))  $settings->agenda_search_filter = $formInput['agenda_search_filter'];

			$settings->save();

		} else {

			$setting = array();

			if (isset($formInput['registration_end_date']) && $formInput['registration_end_date'])
				$setting['registration_end_date'] = date('Y-m-d', strtotime($formInput['registration_end_date']));
			if (isset($formInput['cancellation_date']) && $formInput['cancellation_date'])
				$setting['cancellation_date'] = date('Y-m-d', strtotime($formInput['cancellation_date']));

			$setting['event_id'] = $event_id;

			\App\Models\EventsiteSetting::create($setting);

		}

		if(isset($formInput['registration_form_id']) && $formInput['registration_form_id'] > 0) {

			//Form specific settings
			$setting = \App\Models\EventsiteSetting::where('event_id', '=', $event_id)->where('registration_form_id', $formInput['registration_form_id'])->first();
	
			if($setting) {
				if(isset($formInput['registration_end_date']) && $formInput['registration_end_date']) $setting->registration_end_date = date('Y-m-d', strtotime($formInput['registration_end_date']));
				if(isset($formInput['registration_end_time']) && $formInput['registration_end_date']) $setting->registration_end_time = date('H:i:s', strtotime($formInput['registration_end_time']));
				if(isset($formInput['ticket_left']) && $formInput['ticket_left']) $setting->ticket_left = (int)$formInput['ticket_left'];
				$setting->save();
			}
			
		}



		return true;
	}

 	/**
	 * @param mixed $formInput
	* 
	* @return [type]
	*/
	static public function getCustomFields($formInput)
	{
		$custom_fields = \App\Models\EventCustomField::where('event_id', $formInput['event_id'])
            ->with(['info' => function ($query) use($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }, 'childrenRecursive' => function ($r) {
                return $r->orderBy('sort_order');
            }, 'childrenRecursive.info' => function ($query) use($formInput) {
                return $query->where('languages_id', $formInput['language_id']);
            }])
			->where('parent_id', '0')
			->where('registration_form_id', (int) $formInput['registration_form_id'])
			->orderBy('sort_order', 'ASC')
			->get()
			->toArray();

		$custom_fields = returnArrayKeys($custom_fields, ['info']);

		foreach($custom_fields as $key => $custom_field) {
			$custom_fields[$key] = $custom_field;
			$custom_fields[$key]['children_recursive'] = returnArrayKeys($custom_field['children_recursive'], ['info']);
		}

		return $custom_fields;	
	}

	/**
	 * @param mixed $formInput
	* 
	* @return [type]
	*/
	static public function getTermAndConditions($formInput)
	{
		return \App\Models\EventCardType::where('event_id', $formInput['event_id'])->first();
	}

	/**
	 * @param mixed $formInput
	* 
	* @return [type]
	*/
	static public function getSocialShare($formInput)
	{
		return \App\Models\EventSiteSocialSection::where('event_id', $formInput['event_id'])->where('status', 1)->get();
	}
	
	/**
	 * getRegistrationFormTheme
	 *
	 * @param  mixed $theme_id
	 * @param  mixed $event_id
	 * @return void
	 */
	static public function getRegistrationFormTheme($theme_id, $event_id)
	{
		return \App\Models\RegistrationThemeSetting::where('event_id', $event_id)->where('theme_id', $theme_id)->first();
	}

	/**
	 * get settings
	 *
	 * @param array formInput
	 */
	public static function getSetting($formInput)
	{
		return \App\Models\EventsiteSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', $formInput['registration_form_id'])->first();
	}

	/**
	 * Get cancel status
	 *
	 * @param object $order_detail
	 * @param object settings
	 */
	public static function getCancelStatus($order_detail, $settings)
	{
		$cancel_dateTimeString = \Carbon\Carbon::parse($settings->cancellation_date)->format('Y-m-d') . ' ' . \Carbon\Carbon::parse($settings->cancellation_end_time)->format('H:i:s');

        $current_date = date('Y-m-d');

        $current_time = date('H:i:s');
        
        $current_dateTimeString = \Carbon\Carbon::parse($current_date .' ' .$current_time)->format('Y-m-d H:i:s');

		if ($order_detail && $order_detail->status != 'cancelled' && $order_detail->status != 'rejected') {

			if ($settings->cancellation_date == '0000-00-00 00:00:00') {
				return true;
			} else if (strtotime($current_dateTimeString) <= strtotime($cancel_dateTimeString)) {
				return true;
			} else {
				return false;
			}
	
		} else {
			return false;
		}
	}

	public function getManagePackagesListing($formInput)
	{	
		$eventsite_setting = \App\Models\EventsiteSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', 0)->first();
		if($eventsite_setting['manage_package'] == 0){
			return [];
		}
		$packages = \App\Models\FormPackage::join('conf_registration_form', 'conf_registration_form.id' ,'conf_forms_packages.registration_form_id',)->where('conf_forms_packages.event_id', $formInput['event_id'])->where(['conf_forms_packages.status'=> 1, 'conf_registration_form.status'=>1])->orderBy('conf_forms_packages.sort_order')->get()->toArray();
		foreach ($packages as $key => $package) {
			$eventsite_setting = \App\Models\EventsiteSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', $package['registration_form_id'])->select(['registration_end_date', 'registration_end_time', 'ticket_left'])->first();
			$reg_form = \App\Models\RegistrationForm::where('id', $package['registration_form_id'])->first();
			$sold_tickets= $this->getRegistrationFormTicketsSold($formInput['event_id'], $reg_form);
			$packages[$key]['attendee_type'] = $reg_form['type_id'];
			$packages[$key]['total_tickets'] = $eventsite_setting['ticket_left'];
			$packages[$key]['sold_tickets'] = $sold_tickets;
			$packages[$key]['eventsite_setting'] = $eventsite_setting;
		}
		return $packages;
	}


	public function getRegistrationFormTicketsSold($event_id, $reg_form)
	{	
		$sold_tickets = 0;
		if($reg_form){
			$active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => ['draft', 'completed']], false, true);

			//Validate form stock
			$sold_tickets = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $reg_form['id']], true);
		}
		return $sold_tickets;
	}

	public function getPackageCurrency($formInput)
	{
        $settings = \App\Models\EventsitePaymentSetting::where('event_id', '=', $formInput['event_id'])->first();

		$get_currency = getCurrencyArray();

		$currency = $get_currency[$settings->eventsite_currency];

		return $currency;
	}
}
