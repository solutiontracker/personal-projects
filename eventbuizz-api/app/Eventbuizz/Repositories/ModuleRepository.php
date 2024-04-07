<?php
namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class ModuleRepository extends AbstractRepository
{
	private $request;

	public static $modules_setting = array(
		'agendas' =>
			array(
				'settings' => 'AgendaSetting'
			),
		'attendees' =>
			array(
				'settings' => 'AttendeeSetting'
			),
		'attendee_fields' =>
			array(
				'settings' => 'AttendeeFieldSetting'
			),
        'attendee_field_sorting' =>
            array(
                'settings' => 'EventAttendeeFieldDisplaySorting'
            ),
		'banner' =>
			array(
				'settings' => 'EventBannersSetting'
			),
		'speakers' =>
			array(
				'settings' => 'SpeakerSetting'
			),
        'speakers_field_sorting' =>
            array(
                'settings' => 'EventSpeakerFieldDisplaySorting'
            ),
		'ddirectory' =>
			array(
				'settings' => 'DirectorySetting'
			),
		'subregistration' =>
			array(
				'settings' => 'EventSubRegistrationSetting'
			),
		'sponsors' =>
			array(
				'settings' => 'SponsorSetting'
			),
		'exhibitors' =>
			array(
				'settings' => 'ExhibitorSetting'
			),
		'polls' =>
			array(
				'settings' => 'PollSetting'
			),
		'polltemplate' =>
			array(
				'settings' => 'PollTemplate'
			),
		'qatemplate' =>
			array(
				'settings' => 'QASettingTemplate'
			),
		'qa' =>
			array(
				'settings' => 'QASetting'
			),
		'social' =>
			array(
				'settings' => 'SocialMediaFeedSetting'
			),
		'checkIn' =>
			array(
				'settings' => 'EventCheckInSetting'
			),
		'print' =>
			array(
				'settings' => 'PrintSetting'
			),
		'selfcheckin' =>
			array(
				'settings' => 'PrintSelfCheckIn'
			),
		'gallery' =>
			array(
				'settings' => ''
			),
		'mydocuments' =>
			array(
				'settings' => 'EventDocumentSetting'
			),
		'myturnlist' =>
			array(
				'settings' => 'EventTurnListSetting'
			),
		'social_wall' =>
			array(
				'settings' => 'SocialWallSetting'
			),
		'mobile_app' =>
			array(
				'settings' => ''
			),
		'general_settings' =>
			array(
				'settings' => 'EventsiteSetting'
			),
		'eventsitebaner' =>
			array(
				'settings' => 'EventSiteBannerSetting'
			),
		'payment_settings' =>
			array(
				'settings' => 'EventsitePaymentSetting'
			),
		'payment_card' => array(
		    'settings' => 'EventCardType'
        ),
		'additional_fields' =>
			array(
				'settings' => 'EventsitePaymentSetting'
			),
		'hotel' =>
			array(
				'settings' => 'EventsitePaymentSetting'
			),
		'waiting_list' =>
			array(
				'settings' => 'EventWaitingListSetting'
			),
		'alert_setting' =>
			array(
				'settings' => 'EventAlertSetting'
			),
		'social_media_setting' =>
			array(
				'settings' => 'SocialMediaSetting'
			),
		'custom_field' =>
			array(
				'settings' => 'CustomFieldSetting'
			),
	);

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function getModulesSetting($module)
	{
		return self::$modules_setting[$module];
	}

	public function copyModuleSetting($model, $from_event_id, $to_event_id) {
		if (!empty($model)) {
			$temp_name = 'App\Models\\' . $model;
			if (class_exists($temp_name)) {
				$model_name = $temp_name;
			} else {
				$model_name = "\\" . $model;
			}
			if(in_array($model, ['EventAttendeeFieldDisplaySorting', 'EventSpeakerFieldDisplaySorting'])) {
				$from_settings = $model_name::where('event_id', $from_event_id)->get();
				if(count($from_settings) > 0) {
					$model_name::where('event_id', $to_event_id)->delete();
					foreach($from_settings as $from_setting) {
						$to_setting = new $model_name();
						$to_setting->event_id = $to_event_id;
						$to_setting->fields_name = $from_setting->fields_name;
						$to_setting->sort_order = $from_setting->sort_order;
						if(in_array($model, ['EventAttendeeFieldDisplaySorting'])) {
							$to_setting->is_private = $from_setting->is_private;
						}
						$to_setting->save(); 
					}
				}
			} else {
				$from_settings = $model_name::where('event_id', $from_event_id)->get();
				//Delete old settings
				$model_name::where('event_id', $to_event_id)->delete();
				if(count($from_settings) > 0) {
					foreach($from_settings as $from_setting) {
						$to_setting = $from_setting->replicate();
						$to_setting->event_id = $to_event_id;
						if(in_array($model, ['EventsiteSetting', 'CustomFieldSetting'])) {
							if (session()->has('clone.event.event_registration_form.' . $from_setting->registration_form_id) && $from_setting->registration_form_id > 0) {
								$to_setting->registration_form_id = session()->get('clone.event.event_registration_form.' . $from_setting->registration_form_id);
							}
						}
						$to_setting->save();
					}
				} else {
					$to_setting = new $model_name();
					$to_setting->event_id = $to_event_id;
					$to_setting->save();
				}
			}
		}
	}

	public function cloneEventModulesSetting($modules, $from_event_id, $to_event_id) {

		foreach ($modules as $module) {
			if(is_array($module)) {
				foreach($module as $sub_module) {
					$model = $this->getModulesSetting($sub_module);
					$model = $model['settings'];
					$this->copyModuleSetting($model, $from_event_id, $to_event_id);
				}
			} else {
				$model = $this->getModulesSetting($module);
				$model = $model['settings'];
				$this->copyModuleSetting($model, $from_event_id, $to_event_id);
			}
		}
	}
}