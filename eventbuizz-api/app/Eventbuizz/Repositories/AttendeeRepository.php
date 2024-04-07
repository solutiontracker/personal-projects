<?php

namespace App\Eventbuizz\Repositories;

use App\Models\AddAttendeeLog;
use App\Models\EventAttendeeFieldDisplaySorting;
use App\Models\AttendeeInvite;
use App\Models\AttendeeSetting;
use App\Models\Event;
use App\Models\Country;
use App\Models\EventAgendaTurnList;
use App\Models\EventAttendeePollAuthorityLog;
use App\Models\EventAttendeeType;
use App\Models\EventCustomFieldInfo;
use App\Models\EventGdpr;
use App\Models\EventGdprLog;
use App\Models\EventPollAttendeeResult;
use App\Models\EventSiteText;
use App\Models\EventSubRegistration;
use App\Models\EventSubRegistrationAnswer;
use App\Models\EventSurveyAttendeeResult;
use App\Models\EventSurveyResult;
use App\Models\EventSurveyResultScore;
use App\Models\GdprAttendeeLog;
use App\Models\SocialWallComment;
use App\Models\SocialWallCommentLike;
use App\Models\SocialWallPostLike;
use App\Models\EventSpeakerFieldDisplaySorting;
use App\Models\SpeakerSetting;
use App\Models\WaitingListAttendee;
use App\Models\EventAgendaSpeaker;
use Illuminate\Http\Request;
use \App\Models\Attendee;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Eventbuizz\Repositories\EventRepository;
use App\Eventbuizz\Repositories\ExhibitorRepository;
use App\Eventbuizz\Repositories\SponsorsRepository;
use App\Eventbuizz\Repositories\CheckInOutRepository;
use App\Mail\Email;
use App\Models\EventFoodAllergies;
use App\Models\EventSurveyGroup;
use App\Models\Language;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AttendeeRepository extends AbstractRepository
{
	protected $model;

	protected $eventsiteBillingOrderRepository;

	protected $eventRepository;

	protected $exhibitorRepository;

	protected $sponsorsRepository;

	protected $checkInOutRepository;

	public static $infoFields = array('delegate_number', 'table_number', 'age', 'gender', 'company_name', 'title', 'industry', 'about', 'phone', 'website', 'website_protocol', 'facebook', 'facebook_protocol', 'twitter', 'twitter_protocol', 'linkedin', 'linkedin_protocol', 'linkedin_profile_id', 'registration_type', 'country', 'organization', 'jobs', 'interests', 'allow_vote', 'allow_gallery', 'initial', 'department', 'custom_field_id', 'network_group', 'billing_ref_attendee', 'billing_password', 'ask_to_apeak', 'type_resource', 'place_of_birth', 'passport_no', 'date_of_issue_passport', 'date_of_expiry_passport', 'private_house_number', 'private_street', 'private_post_code', 'private_city', 'private_country');

	public function __construct(Request $request, Attendee $model, EventsiteBillingOrderRepository $eventsiteBillingOrderRepository, EventRepository $eventRepository, ExhibitorRepository $exhibitorRepository, SponsorsRepository $sponsorsRepository, CheckInOutRepository $checkInOutRepository)
	{
		$this->request = $request;
		$this->model = $model;
		$this->eventsiteBillingOrderRepository = $eventsiteBillingOrderRepository;
		$this->eventRepository = $eventRepository;
		$this->exhibitorRepository = $exhibitorRepository;
		$this->sponsorsRepository = $sponsorsRepository;
		$this->checkInOutRepository = $checkInOutRepository;
	}

	/**
	 * Event installation / Clone attendee fields when cloning / Default attendee fields from master table when event create.
	 *
	 * @param array
	 */

	public function install($request)
	{
		//Attendee content
		if ($request["content"]) {

			//Event Attendees
			$from_event_attendees = \App\Models\EventAttendee::where("event_id", $request['from_event_id'])
				->where(function ($query) {
					$query->where("speaker", 1);
					$query->orWhere("sponser", 1);
					$query->orWhere("exhibitor", 1);
				})
				->get();

			if ($from_event_attendees) {
				foreach ($from_event_attendees as $from_event_attendee) {
					$to_event_attendee = $from_event_attendee->replicate();
					$to_event_attendee->event_id = $request['to_event_id'];
					if (session()->has('clone.event.event_attendee_types.' . $from_event_attendee->attendee_type)) {
						$to_event_attendee->attendee_type = session()->get('clone.event.event_attendee_types.' . $from_event_attendee->attendee_type);
					} else {
						$to_event_attendee->attendee_type = "";
					}
					$to_event_attendee->save();

					session()->put('clone.event.event_attendees.' . $from_event_attendee->id, $to_event_attendee->id);

					//Event Speaker Categories
					if ($from_event_attendee->speaker == 1) {
						$from_event_speaker_categories = \App\Models\EventSpeakerCategory::where("speaker_id", $from_event_attendee->attendee_id)->get();
						if ($from_event_speaker_categories) {
							foreach ($from_event_speaker_categories as $from_event_speaker_category) {
								if (session()->has('clone.event.event_categories.' . $from_event_speaker_category->category_id)) {
									$to_event_speaker_category = $from_event_speaker_category->replicate();
									$to_event_speaker_category->category_id = session()->get('clone.event.event_categories.' . $from_event_speaker_category->category_id);
									$to_event_speaker_category->speaker_id = $to_event_attendee->attendee_id;
									$to_event_speaker_category->save();
								}
							}
						}
					}

					//Event Attendee Groups
					$from_event_attendee_groups = \App\Models\EventAttendeeGroup::where("attendee_id", $from_event_attendee->attendee_id)->get();
					if ($from_event_attendee_groups) {
						foreach ($from_event_attendee_groups as $from_event_attendee_group) {
							if (session()->has('clone.event.event_groups.' . $from_event_attendee_group->group_id)) {
								$to_event_attendee_group = $from_event_attendee_group->replicate();
								$to_event_attendee_group->linked_from = "";
								$to_event_attendee_group->group_id = session()->get('clone.event.event_groups.' . $from_event_attendee_group->group_id);
								$from_event_attendee_group->attendee_id = $to_event_attendee->attendee_id;
								$to_event_attendee_group->save();
							}
						}
					}
				}
			}

			//Event Agenda Speakers
			$from_event_agenda_speakers = \App\Models\EventAgendaSpeaker::where("event_id", $request['from_event_id'])->get();
			if ($from_event_agenda_speakers) {
				foreach ($from_event_agenda_speakers as $from_event_agenda_speaker) {
					$to_event_agenda_speaker = $from_event_agenda_speaker->replicate();
					if (session()->has('clone.event.programs.' . $from_event_agenda_speaker->agenda_id)) {
						$to_event_agenda_speaker->event_id = $request['to_event_id'];
						$to_event_agenda_speaker->agenda_id = session()->get('clone.event.programs.' . $from_event_agenda_speaker->agenda_id);
						$to_event_agenda_speaker->save();
					}
				}
			}

		}
	}

	/**
	 * create event attendee types
	 *
	 * @param array
	 */
	public function createTypes($request)
	{
		$alias = array('attendee', 'sponsor', 'exhibitor', 'speaker');

		$languages = array(
			"1" => array('Attendee', 'Sponsor', 'Exhibitor', 'Speaker'),
			"2" => array('deltager', 'Sponsor', 'Udstiller', 'Taler'),
			"3" => array('Deltaker', 'Sponsor', 'utstiller', 'Snakker'),
			"4" => array('Teilnehmer', 'Sponsor', 'Aussteller', 'Sprechen'),
			"5" => array('Dalyvis', 'Rėmėjas', 'Dalyvis', 'Kalbėjimas'),
			"6" => array('Osallistuja', 'Sponsori', 'näytteilleasettaja', 'Puhuminen'),
			"7" => array('Deltagare', 'Sponsor', 'Utställare', 'På tal'),
			"8" => array('deelnemer', 'Sponsor', 'Exposant', 'Sprekend'),
			"9" => array('deelnemer', 'Sponsor', 'Exposant', 'Sprekend')
		);

		foreach ($languages as $language_id => $types) {
			$i = 0;
			foreach ($types as $type) {
				$data['event_id'] = $request['to_event_id'];
				$data['languages_id'] = $language_id;
				$data['alias'] = $alias[$i];
				$data['attendee_type'] = $type;
				$data['is_basic'] = 1;
				$data['status'] = 1;
				\App\Models\EventAttendeeType::create($data);
				$i++;
			}
		}
	}

	/**
	 * Save data for attendee
	 *
	 * @param array
	 */

	public function store($formInput)
	{
		$formInput = get_trim_all_data($formInput, 'attendee');
		$instance = $this->setCreateForm($formInput);
		$attendee = \App\Models\Attendee::where('organizer_id', organizer_id())->where('email', $formInput['email'])->first();
		if ($attendee) {
			$event_attendee = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', $formInput['event_id'])->count();

			$instance->updateAttendee($attendee);

			//Assign attendee to event
			if ($event_attendee == 0) {
				$instance->insertEvent();
			}

			$instance->updateInfo($attendee->id);
			$instance->detachedGroups($attendee->id);
			$instance->insertGroups();

			//check if attendee as a speaker
			if (isset($formInput['speaker']) && $formInput['speaker']) {
				$instance->detachedCategories($attendee->id);
				$instance->insertCategories();
				$instance->detachedProgram($attendee->id);
				$instance->attachProgram();
			}
		} else {
			$instance->create();
			$instance->insertInfo();
			$instance->insertImage();
			$instance->insertEvent();
			$instance->insertGroups();

			//check if attendee as a speaker
			if (isset($formInput['speaker']) && $formInput['speaker']) {
				$instance->insertCategories();
				$instance->attachProgram();
				EventRepository::add_module_progress($formInput, "speaker");
			} else {
				EventRepository::add_module_progress($formInput, "attendee");
			}

		}
	}

	/**
	 * set form values
	 *
	 * @param array
	 */

	public function setCreateForm($formInput)
	{
		$formInput['email'] = $formInput['email'];
		$event_info = \App\Models\Event::find($formInput['event_id'])->info()->get();
		$attendee_setting = \App\Models\AttendeeSetting::where('event_id', '=', $formInput['event_id'])->first();
		$default_password = $attendee_setting->default_password;
		$password = ($default_password ? $default_password : '123456');
		foreach ($event_info as $key) {
			if ($key->name == 'attendee_password' && $key->value) $password = $key->value;
		}
		$formInput['password'] = \Hash::make($password);
		$formInput['status'] = '1';
		$formInput['change_password'] = '1';
		$formInput['organizer_id'] = organizer_id();
		$formInput['image'] = (isset($formInput['image_name']) && $formInput['image_name'] ? $formInput['image_name'] : '');
		if (isset($formInput['SPOKEN_LANGUAGE']) && is_array($formInput['SPOKEN_LANGUAGE']) && count($formInput['SPOKEN_LANGUAGE'] ?? []) > 0)	 $formInput['SPOKEN_LANGUAGE'] = implode(',', $formInput['SPOKEN_LANGUAGE']);
		if (isset($formInput['EMPLOYMENT_DATE']) && $formInput['EMPLOYMENT_DATE'])
			$formInput['EMPLOYMENT_DATE'] = date('Y-m-d', strtotime($formInput['EMPLOYMENT_DATE']));
		if (isset($formInput['BIRTHDAY_YEAR']) && $formInput['BIRTHDAY_YEAR'])
			$formInput['BIRTHDAY_YEAR'] = date('Y-m-d', strtotime($formInput['BIRTHDAY_YEAR']));
		if (isset($formInput['phone']) && isset($formInput['calling_code']) && $formInput['phone'])
			$formInput['phone'] = '+' . ltrim($formInput['calling_code'], '+') . '-' . $formInput['phone'];
		if (isset($formInput['ss_number']) && $formInput['ss_number'])
			$formInput['ss_number'] = md5(str_replace('-', '', $formInput['ss_number']));
		if (isset($formInput['type_resource']) && $formInput['type_resource'])
			$formInput['type_resource'] = $formInput['type_resource'];
		else
			$formInput['type_resource'] = 0;

		if (isset($formInput['custom_field_id']) && count($formInput['custom_field_id'] ?? []) > 0 && is_array($formInput['custom_field_id'])) {
			$formInput['custom_field_id'] = implode(',', $formInput['custom_field_id']);
		}

		$formInput['linkedin_profile_id'] = (isset($formInput['linkedin_profile_id']) ? $formInput['linkedin_profile_id'] : '');
		$formInput['registration_type'] = (isset($formInput['registration_type']) ? $formInput['registration_type'] : '');
		$formInput['billing_ref_attendee'] = (isset($formInput['billing_ref_attendee']) ? $formInput['billing_ref_attendee'] : '');
		$formInput['billing_password'] = (isset($formInput['billing_password']) ? $formInput['billing_password'] : '');
		$formInput['allow_vote'] = (isset($formInput['allow_vote']) && $formInput['allow_vote'] ? $formInput['allow_vote'] : 0);
		$formInput['allow_gallery'] = (isset($formInput['allow_gallery']) && $formInput['allow_gallery'] ? $formInput['allow_gallery'] : 0);
		$formInput['ask_to_apeak'] = (isset($formInput['ask_to_apeak']) && $formInput['ask_to_apeak'] ? $formInput['ask_to_apeak'] : 0);
		$formInput['allow_my_document'] = (isset($formInput['allow_my_document']) && $formInput['allow_my_document'] ? $formInput['allow_my_document'] : 0);
		$this->setFormInput($formInput);
		return $this;
	}

	/**
	 * Save info for attendee
	 *
	 * @param array
	 */

	public function insertInfo()
	{
		$formInput = $this->getFormInput();
		$languages = get_event_languages($formInput['event_id']);
		$info = array();
		foreach ($languages as $key) {
			foreach (self::$infoFields as $field) {
				$value = (isset($formInput[$field]) ? $formInput[$field] : '');
				$info[] = new \App\Models\AttendeeInfo(array('name' => $field, 'value' => trim($value), 'languages_id' => $key, 'status' => 1));
			}
		}

		$attendee_obj = $this->getObject();
		$attendee_obj->info()->saveMany($info);
		return $this;
	}

	/**
	 * Save categories for attendee
	 *
	 * @param array
	 */
	public function insertCategories()
	{
		$formInput = $this->getFormInput();

		$attendee = $this->getObject();
		$model = \App\Models\Attendee::find($attendee->id);
		if (isset($formInput['categories']) && count($formInput['categories'] ?? []) > 0) {
			foreach ($formInput['categories'] as $id) {
				$model->categories()->attach($id, array('status' => 0, 'updated_at' => \Carbon\Carbon::now()));
			}
		}
		return $this;
	}

	/**
	 * Save Image for attendee
	 *
	 * @param array
	 */
	public function insertImage()
	{
		$formInput = $this->getFormInput();
		if (isset($formInput['allow_gallery'])) {
			$model = new \App\Models\EventAttendeeImage(array('event_id' => $formInput['event_id']));
			$attendee = $this->getObject();
			$attendee->image()->save($model);
		}
		return $this;
	}

	/**
	 * Attach event with attendee
	 *
	 * @param array
	 */
	public function insertEvent()
	{
		$formInput = $this->getFormInput();

		//check if attendee as a speaker
		$speaker = (isset($formInput['speaker']) && $formInput['speaker'] ? '1' : '0');
        $gdpr=0;
		//gdpr setting
		$gdpr_accepted = \App\Models\SpeakerSetting::where('event_id', '=', $formInput['event_id'])->value('gdpr_accepted');
		if(isset($formInput['gdpr'])){
            $gdpr=$formInput['gdpr'];
        }else{
            $gdpr = ($gdpr_accepted ? 1 : 0);
        }
		$model = new \App\Models\EventAttendee(array('event_id' => $formInput['event_id'], 'default_language_id' => $formInput['language_id'], 'status' => 1, 'attendee_type' => (isset($formInput['attendee_type_id']) ? $formInput['attendee_type_id'] : 0), 'email_sent' => 0, 'sms_sent' => 0, 'login_yet' => 0, 'verification_id' => '', 'allow_vote' => $formInput['allow_vote'], 'allow_gallery' => $formInput['allow_gallery'], 'ask_to_apeak' => $formInput['ask_to_apeak'], 'type_resource' => $formInput['type_resource'], 'allow_my_document' => $formInput['allow_my_document'], 'speaker' => $speaker, 'gdpr' => $gdpr));
		$attendee = $this->getObject();
        $eventGdpr = EventGdpr::where('event_id', $formInput['event_id'])->get()->first();
        GdprAttendeeLog::create([
            'event_id' => $formInput['event_id'],
            'attendee_id' => $attendee['id'],
            'gdpr_accept' => $gdpr,
            'gdpr_description' => $eventGdpr->description,
            'admin_id' => organizer_id()
        ]);

		$attendee->event()->save($model);

		return $this;
	}

	/**
	 * Attach groups with attendee
	 *
	 * @param array
	 */
	public function insertGroups()
	{
		$groups = array();
		$formInput = $this->getFormInput();
		$attendee = $this->getObject();
		$model = \App\Models\Attendee::find($attendee->id);
		if (isset($formInput['parent_group_id'])) {
			foreach ($formInput['parent_group_id'] as $p_group_id) {
				$groups[] = $p_group_id;
			}
		}
		if (isset($formInput['group_id']) && $formInput['group_id']) {
			$groups = array_merge($formInput['group_id'], $groups);
		}
		foreach ($groups as $group_id) {
			$model->groups()->attach($group_id, array('updated_at' => \Carbon\Carbon::now()));
		}

		return $this;
	}

	/**
	 * Attach program with speaker
	 *
	 * @param array
	 */
	public function attachProgram()
	{
		$formInput = $this->getFormInput();

		$max = \App\Models\EventAgendaSpeaker::where('event_id', '=', $formInput['event_id'])->max('sort_order');

		$attendee = $this->getObject();
		$data['attendee_id'] = $attendee->id;
		$data['agenda_id'] = $formInput['program_id'];
		$data['event_id'] = $formInput['event_id'];
		$data['sort_order'] = ($max + 1);

		\App\Models\EventAgendaSpeaker::create($data);
	}

	/**
	 * Add attendee log info
	 *
	 * @param array
	 */
	public function insertLog()
	{
		$formInput = $this->getFormInput();

		$attendee = $this->getObject();
	}

	/**
	 * Update data for attendee
	 *
	 * @param array
	 * @param id
	 */

	public function edit($formInput, $attendee)
	{
		$formInput = get_trim_all_data($formInput, 'attendee');
		$instance = $this->setUpdateForm($formInput);
		$instance->updateAttendee($attendee);
		$instance->updateInfo($attendee->id);
		$instance->detachedGroups($attendee->id);
		$instance->insertGroups();
		$instance->updateEvent($attendee->id);
	}

	/**
	 * Set form values for update attendee
	 *
	 * @param array
	 */

	public function setUpdateForm($formInput)
	{
		$formInput['email'] = $formInput['email'];
		if (isset($formInput['SPOKEN_LANGUAGE']) && is_array($formInput['SPOKEN_LANGUAGE']) && count($formInput['SPOKEN_LANGUAGE'] ?? []) > 0)	 $formInput['SPOKEN_LANGUAGE'] = implode(',', $formInput['SPOKEN_LANGUAGE']);
		if (isset($formInput['EMPLOYMENT_DATE']) && $formInput['EMPLOYMENT_DATE'])
			$formInput['EMPLOYMENT_DATE'] = date('Y-m-d', strtotime($formInput['EMPLOYMENT_DATE']));
		if (isset($formInput['BIRTHDAY_YEAR']) && $formInput['BIRTHDAY_YEAR'])
			$formInput['BIRTHDAY_YEAR'] = date('Y-m-d', strtotime($formInput['BIRTHDAY_YEAR']));
		if (isset($formInput['phone']) && isset($formInput['calling_code']) && $formInput['phone'])
			$formInput['phone'] = '+' . ltrim($formInput['calling_code'], '+') . '-' . $formInput['phone'];
		if (isset($formInput['ss_number']) && $formInput['ss_number']) {
			$formInput['ss_number'] = md5(str_replace('-', '', $formInput['ss_number']));
		} else {
			unset($formInput['ss_number']);
		}
		if (isset($formInput['type_resource']) && $formInput['type_resource'])
			$formInput['type_resource'] = $formInput['type_resource'];
		else
			$formInput['type_resource'] = 0;

		if (isset($formInput['custom_field_id']) && count($formInput['custom_field_id'] ?? []) > 0) {
			$formInput['custom_field_id'] = implode(',', $formInput['custom_field_id'] ?? []);
		}

		$formInput['image'] = (isset($formInput['image_name']) && $formInput['image_name'] ? $formInput['image_name'] : '');

		$this->setFormInput($formInput);
		return $this;
	}

	/**
	 * Update info for attendee
	 *
	 * @param array
	 * @param int
	 */

	public function updateInfo($id)
	{
		$formInput = $this->getFormInput();

		$languages = get_event_languages($formInput['event_id']);
		if (!isset($formInput['linkedin_profile_id'])) $formInput['linkedin_profile_id'] = '';
		if (!isset($formInput['registration_type'])) $formInput['registration_type'] = '';
		if (!isset($formInput['billing_ref_attendee'])) $formInput['billing_ref_attendee'] = '';
		if (!isset($formInput['billing_password'])) $formInput['billing_password'] = '';
		if (!isset($formInput['allow_vote'])) $formInput['allow_vote'] = 0;
		if (!isset($formInput['allow_gallery'])) $formInput['allow_gallery'] = 0;
		if (!isset($formInput['ask_to_apeak'])) $formInput['ask_to_apeak'] = 0;
		if (!isset($formInput['type_resource'])) $formInput['type_resource'] = 0;
		foreach ($languages as $key) {
			foreach (self::$infoFields as $field) {
				if (isset($formInput[$field])) {
					$info = \App\Models\AttendeeInfo::where('attendee_id', '=', $id)->where('languages_id', '=', $key)->where('name', '=', $field)->first();
					if (!$info) {
						\App\Models\AttendeeInfo::create([
							"name" => $field,
							"attendee_id" => $id,
							"languages_id" => $key,
							"value" => trim($formInput[$field]),
						]);
					} else {
						$info->value =  trim($formInput[$field]);
						$info->save();
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Detach groups for attendee
	 *
	 * @param array
	 * @param int
	 */
	public function detachedGroups($id)
	{
		$formInput = $this->getFormInput();

		$groups = \App\Models\EventGroup::where('event_id', $formInput['event_id'])->where('parent_id', '!=', 0)->pluck('id');
		\App\Models\EventAttendeeGroup::where('attendee_id', $id)->wherein('group_id', $groups->toArray())->delete();
		return $this;
	}

	/**
	 * Detach program for attendee
	 *
	 * @param array
	 * @param int
	 */
	public function detachedProgram($id)
	{
		$formInput = $this->getFormInput();
		\App\Models\EventAgendaSpeaker::where('attendee_id', $id)->where('agenda_id', $formInput['program_id'])->where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();
		return $this;
	}

	/**
	 * Detach Categories for attendee/speaker
	 *
	 * @param array
	 * @param int
	 */

	public function detachedCategories($id)
	{
		\App\Models\EventSpeakerCategory::where('speaker_id', $id)->delete();
		return $this;
	}

	/**
	 *Update event for attendee
	 *
	 * @param array
	 * @param int
	 */
	public function updateEvent($id)
	{
		$formInput = $this->getFormInput();

		$model = \App\Models\EventAttendee::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->first();
		if (isset($formInput['attendee_type_id'])) $model->attendee_type = $formInput['attendee_type_id'];
		if (isset($formInput['email_sent'])) $model->email_sent = $formInput['email_sent'];
		if (isset($formInput['sms_sent'])) $model->sms_sent = $formInput['sms_sent'];
		if (isset($formInput['login_yet'])) $model->login_yet = $formInput['login_yet'];
		if (isset($formInput['allow_vote'])) $model->allow_vote = $formInput['allow_vote'];
		if (isset($formInput['allow_gallery'])) $model->allow_gallery = $formInput['allow_gallery'];
		if (isset($formInput['ask_to_apeak'])) $model->ask_to_apeak = $formInput['ask_to_apeak'];
		if (isset($formInput['gdpr'])) $model->gdpr = $formInput['gdpr'];
		if (isset($formInput['type_resource'])) $model->type_resource = $formInput['type_resource'];
		if (isset($formInput['allow_my_document'])) $model->allow_my_document = $formInput['allow_my_document'];
		if (isset($formInput['speaker'])) $model->speaker = $formInput['speaker'];
		if(isset($formInput['gdpr'])){
		    $check=GdprAttendeeLog::where('attendee_id',$id)->orderBy('id','desc')->first();
            $eventGdpr = EventGdpr::where('event_id', $formInput['event_id'])->get()->first();
		    if($check ){
		        if($check->gdpr_accept!=$formInput['gdpr']){
                    GdprAttendeeLog::create([
                        'event_id' => $formInput['event_id'],
                        'attendee_id' => $id,
                        'gdpr_accept' => $formInput['gdpr'],
                        'gdpr_description' => $eventGdpr->description,
                        'admin_id' => organizer_id()
                    ]);
                }
            }else{
                GdprAttendeeLog::create([
                    'event_id' => $formInput['event_id'],
                    'attendee_id' => $id,
                    'gdpr_accept' => $formInput['gdpr'],
                    'gdpr_description' => $eventGdpr->description,
                    'admin_id' => organizer_id()
                ]);
            }
        }
		$model->save();

		return $this;
	}

	/**
	 *Update log for attendee
	 *
	 * @param array
	 * @param object
	 */
	public function updateAttendee($attendee)
	{
		$formInput = $this->getFormInput();
		$attendee->fill($formInput);
		$attendee->save();
	}

	/**
	 *Destroy attendee
	 *
	 * @param array
	 * @param int
	 */

	public function destroy($formInput, $id)
	{
		$order_exists = false;
		$order_detail = $this->eventsiteBillingOrderRepository->attendeeOrder($id, $formInput['event_id']);

		if ($order_detail && ($this->eventsiteBillingOrderRepository->isOrderWithoutItems($order_detail->id) == false) && $order_detail->event_id == $formInput['event_id']) {
			$order_exists = true;
			$order_id = $order_detail->order_number;
		}

		if ($order_exists && $order_id) {
			$label = __('messages.on_attendee_delete');
			$msg = sprintf($label, $order_id, $order_id);
			return array(
				'status' => false,
				'order_id' => $order_id,
				'message' => $msg
			);
		} else {
			if ($order_detail && $this->eventsiteBillingOrderRepository->isOrderWithoutItems($order_detail->id)) {
				\App\Models\BillingOrder::where('id', $order_detail->id)->update(['is_archive' => 1]); //archive order if any
			}

			//delete attendee groups
			$event_groups = \App\Models\EventGroup::join('conf_event_attendees_groups', 'conf_event_attendees_groups.group_id', '=', 'conf_event_groups.id')
				->where('conf_event_groups.event_id', $formInput['event_id'])
				->where('conf_event_attendees_groups.attendee_id', $id)
				->pluck('conf_event_attendees_groups.group_id')
				->toArray();

			\App\Models\EventAttendeeGroup::whereIn('group_id', $event_groups)->where('attendee_id', '=', $id)->delete();

			\App\Models\QA::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->delete();
			\App\Models\HelpDesk::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->delete();
			\App\Models\Directory::where('event_id', '=', $formInput['event_id'])->where('parent_id', '!=', '0')->where('speaker_id', '=', $id)->delete();

			/********** Delete Poll Results *****************/
			\App\Models\EventPollResult::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->delete();
			\App\Models\EventPollResultScore::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->delete();
			EventPollAttendeeResult::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();
			EventAttendeePollAuthorityLog::where('event_id', $formInput['event_id'])->orwhere(function ($q) use ($formInput) {
				$q->where('attendee_to', $formInput['attendee_id'])->orWhere('attendee_from', $formInput['attendee_id']);
			})->delete();

			/***************** Delete Event Survey Results *********************/
			EventSurveyResult::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();
			EventSurveyAttendeeResult::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();
			EventSurveyResultScore::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();

			/*************** Social Wall Posts *********************/
			\App\Models\SocialWallPost::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->delete();
			$social_wall_ids =  \App\Models\SocialWallPost::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->pluck('id')->toArray();
			SocialWallComment::whereIn('post_id', $social_wall_ids)->delete();
			SocialWallCommentLike::whereIn('post_id', $social_wall_ids)->delete();
			SocialWallPostLike::whereIn('post_id', $social_wall_ids)->delete();

			$event_attendee = \App\Models\EventAttendee::where('event_id', '=', $formInput['event_id'])->where('attendee_id', $id)->first();
            if($event_attendee){
                $event_attendee->delete();
            }
			\App\Models\EventSubRegistrationResult::where('event_id', '=', $formInput['event_id'])->where('attendee_id', $id)->delete();
			\App\Models\EventAgendaSpeaker::where('event_id', '=', $formInput['event_id'])->where('attendee_id', $id)->delete();

			EventAgendaTurnList::where('attendee_id', $id)->whereIn('agenda_id', \App\Models\EventAgenda::where('event_id', $formInput['event_id'])->pluck('id')->toArray())->delete();

			GdprAttendeeLog::where('attendee_id', $id)->where('event_id', $formInput['event_id']);

			WaitingListAttendee::where('attendee_id', $id)->where('event_id', $formInput['event_id']);


            ExhibitorRepository::deleteExhibtorContact($id, $formInput['event_id']);
            SponsorsRepository::deleteSponsorContact($id, $formInput['event_id']);

			//Delete categories
			\App\Models\EventSpeakerCategory::where('speaker_id', $id)->delete();

			//Event Attendees
			$attendee_events = EventRepository::attendee_events($id);

			$attendee_email = \App\Models\Attendee::where('id', $id)->value('email');

			\App\Models\AttendeeInvite::where('email', $attendee_email)->where('event_id', $formInput['event_id'])->delete();

			if ($attendee_events && $attendee_events->count() > 1) {
				//unsigned attendees from all programs that belongs to this event
				\App\Models\EventAgendaAttendeeAttached::where('attendee_id', $id)->whereIn('agenda_id', \App\Models\EventAgenda::where('event_id', $formInput['event_id'])->pluck('id')->toArray())->delete();

				return array(
					'status' => true,
					'message' => sprintf(__('messages.un_assign'), __('messages.attributes.attendee'))
				);
			} else {
				\App\Models\EventAgendaAttendeeAttached::where('attendee_id', $id)->delete();
				$attendee_orders = \App\Models\BillingOrder::where('attendee_id', '=', $id)->get()->toArray();
				$inserted_already = false;
				if (count($attendee_orders ?? []) > 0) {
					$inserted_already = true;
					foreach ($attendee_orders as $order) {
						$orderObj = \App\Models\BillingOrder::find($order['id']);
						$orderObj->delete();
						\App\Models\AttendeeDeletionLog::insert([
							'event_id' => $formInput['event_id'],
							'attendee_id' => $id,
							'order_id' => $order['id'],
							'additional_attendee' => 0,
							'date' => \Carbon\Carbon::now(),
						]);
					}
				} else {
					$orderAttendees =  \App\Models\BillingOrderAttendee::where('attendee_id', '=', $id)->get()->toArray();
					if (count($orderAttendees ?? []) > 0) {
						$inserted_already = true;
						foreach ($orderAttendees as $order) {
							$orderObj =  \App\Models\BillingOrderAttendee::find($order['id']);
							$orderObj->delete();
							\App\Models\AttendeeDeletionLog::insert([
								'event_id' => $formInput['event_id'],
								'attendee_id' => $id,
								'order_id' => $order['order_id'],
								'additional_attendee' => 1,
								'date' => \Carbon\Carbon::now(),
							]);
						}
					}
				}

				if (!$inserted_already) {
					\App\Models\AttendeeDeletionLog::insert([
						'event_id' => $formInput['event_id'],
						'attendee_id' => $id,
						'order_id' => 0,
						'additional_attendee' => 0,
						'date' => \Carbon\Carbon::now(),
					]);
				}

				$attendee = \App\Models\Attendee::find($id);
				if($attendee){
                    $attendee->delete();
                }


				return array(
					'status' => true,
					'message' => __('messages.delete'),
				);
			}
		}
	}

	/**
	 * @param mixed $formInput
	 * @param mixed $id
	 *
	 * @return [type]
	 */
	public static function unAssign($formInput, $id, $order_cancel = true)
	{
		//Make order cancel
		if($order_cancel) {

			$order = \App\Models\BillingOrder::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->where('status', '!=', 'cancelled')->first();
			
			if ($order) {
				$order->status = 'cancelled';
				$order->is_updated = '1';
				$order->save();
	
				//Create Credit Note
				EventsiteBillingOrderRepository::generateCreditNote($order);
			}
		}
		//Make order cancel end

		//Delete attendee groups
		$event_groups = \App\Models\EventGroup::join('conf_event_attendees_groups', 'conf_event_attendees_groups.group_id', '=', 'conf_event_groups.id')
			->where('conf_event_groups.event_id', $formInput['event_id'])
			->where('conf_event_attendees_groups.attendee_id', $id)
			->pluck('conf_event_attendees_groups.group_id')
			->toArray();

		\App\Models\EventAttendeeGroup::whereIn('group_id', $event_groups)->where('attendee_id', '=', $id)->delete();

		\App\Models\QA::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->delete();
		\App\Models\HelpDesk::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->delete();
		\App\Models\Directory::where('event_id', '=', $formInput['event_id'])->where('parent_id', '!=', '0')->where('speaker_id', '=', $id)->delete();

		/********** Delete Poll Results *****************/
		\App\Models\EventPollResult::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->delete();
		\App\Models\EventPollResultScore::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->delete();
		EventPollAttendeeResult::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();
		EventAttendeePollAuthorityLog::where('event_id', $formInput['event_id'])->orwhere(function ($q) use ($formInput) {
			$q->where('attendee_to', $formInput['attendee_id'])->orWhere('attendee_from', $formInput['attendee_id']);
		})->delete();

		/***************** Delete Event Survey Results *********************/
		EventSurveyResult::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();
		EventSurveyAttendeeResult::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();
		EventSurveyResultScore::where('event_id', $formInput['event_id'])->where('attendee_id', $id)->delete();

		/*************** Social Wall Posts *********************/
		\App\Models\SocialWallPost::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->delete();
		$social_wall_ids =  \App\Models\SocialWallPost::where('event_id', $formInput['event_id'])->where('attendee_id', '=', $id)->pluck('id')->toArray();
		SocialWallComment::whereIn('post_id', $social_wall_ids)->delete();
		SocialWallCommentLike::whereIn('post_id', $social_wall_ids)->delete();
		SocialWallPostLike::whereIn('post_id', $social_wall_ids)->delete();

		$event_attendee = \App\Models\EventAttendee::where('event_id', '=', $formInput['event_id'])->where('attendee_id', $id)->first();
		if($event_attendee){
            $event_attendee->delete();
        }
		\App\Models\EventSubRegistrationResult::where('event_id', '=', $formInput['event_id'])->where('attendee_id', $id)->delete();
		\App\Models\EventAgendaSpeaker::where('event_id', '=', $formInput['event_id'])->where('attendee_id', $id)->delete();

		EventAgendaTurnList::where('attendee_id', $id)->whereIn('agenda_id', \App\Models\EventAgenda::where('event_id', $formInput['event_id'])->pluck('id')->toArray())->delete();

		GdprAttendeeLog::where('attendee_id', $id)->where('event_id', $formInput['event_id']);

		WaitingListAttendee::where('attendee_id', $id)->where('event_id', $formInput['event_id']);

		ExhibitorRepository::deleteExhibtorContact($id, $formInput['event_id']);
        SponsorsRepository::deleteSponsorContact($id, $formInput['event_id']);

		//Delete categories
		\App\Models\EventSpeakerCategory::where('speaker_id', $id)->delete();


        /************ Delete Internal Bookings ****/
        \App\Models\EventInternalBookingHotelAssigned::where('attendee_id',$id)
            ->whereHas('internal_booking_hotel',function ($query) use ($formInput){
                $query->where('event_id',$formInput['event_id']);
            })->delete();
        \App\Models\EventInternalBookingHotelRoomsAssigned::where('attendee_id',$id)->whereEventId($formInput['event_id'])->delete();


        /************ Survey Results and News updates Delete ****/
        $alert_data =  \App\Models\EventAlertIndividual::where('attendee_id', '=', $id)->whereNull('deleted_at')->pluck('alert_id');

        if(count($alert_data) > 0){

            \App\Models\EventAlert::whereEventId($formInput['event_id'])->whereIn('id',$alert_data)->where('sendto','individuals')->delete();
            \App\Models\EventAlertInfo::whereIn('id',$alert_data)->delete();
            \App\Models\EventAlertIndividual::where('attendee_id', '=', $id)->delete();
        }
		//Event Attendees
		$attendee_events = EventRepository::attendee_events($id);

		$attendee_email = \App\Models\Attendee::where('id', $id)->value('email');

		\App\Models\AttendeeInvite::where('email', $attendee_email)->where('event_id', $formInput['event_id'])->delete();

		if ($attendee_events && $attendee_events->count() > 1) {
			//unsigned attendees from all programs that belongs to this event
			\App\Models\EventAgendaAttendeeAttached::where('attendee_id', $id)->whereIn('agenda_id', \App\Models\EventAgenda::where('event_id', $formInput['event_id'])->pluck('id')->toArray())->delete();

			return array(
				'status' => true,
				'message' => sprintf(__('messages.un_assign'), __('messages.attributes.attendee'))
			);
		} else {
			\App\Models\EventAgendaAttendeeAttached::where('attendee_id', $id)->delete();

			return array(
				'status' => true,
				'message' => __('messages.delete'),
			);
		}
	}

	/**
	 *Attendee listing
	 *
	 * @param array
	 */

	public function listing($formInput)
	{
		//Event Attendees
		$result = \App\Models\Event::find($formInput['event_id'])->attendees();

		//fetch attendees as speaker
		if (isset($formInput['speaker']) && $formInput['speaker'] == 1) $result->where('speaker', 1);

		//fetch attendees for specific date
		if (isset($formInput['created_at']) && $formInput['created_at']) $result->whereDate('conf_event_attendees.created_at', $formInput['created_at']);

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('conf_event_attendees.created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('conf_event_attendees.created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}
		if (isset($formInput['attendee_type']) && $formInput['attendee_type'] && $formInput['attendee_type'] !='select') {
			$result->where('conf_event_attendees.attendee_type', $formInput['attendee_type']);
		}

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_attendees.id')
				->where('conf_attendees_info.languages_id', '=', $formInput['language_id'])
				->where(function ($query) use ($formInput) {
					$query->where(function ($query) use ($formInput) {
						$query->where('conf_attendees_info.name', '=', 'network_group')
							->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
					})
						->orWhere(function ($query) use ($formInput) {
							$query->where('conf_attendees_info.name', '=', 'delegate_number')
								->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
						})
						->orWhere(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . trim($formInput['query']) . '%')
						->orWhere('email', 'LIKE', '%' . trim($formInput['query']) . '%');
				});
		}

		//Active orders
		$order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->where('is_archive', 0)->where('conf_billing_orders.is_waitinglist', '=', '0')->currentOrder()->pluck('id');

		if (isset($formInput['type']) && $formInput['type'] == "registration-sign-ups") {
			$result->join('conf_billing_order_attendees', function ($join) use ($order_ids) {
				$join->on('conf_billing_order_attendees.attendee_id', '=', 'conf_attendees.id')
					->whereIn('conf_billing_order_attendees.order_id', $order_ids);
			});

			$result->join('conf_billing_orders', function ($join) use ($order_ids) {
				$join->on('conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id')
					->whereIn('conf_billing_orders.id', $order_ids);
			});
		} else {
			$result->leftJoin('conf_billing_order_attendees', function ($join) use ($order_ids) {
				$join->on('conf_billing_order_attendees.attendee_id', '=', 'conf_attendees.id')
					->whereIn('conf_billing_order_attendees.order_id', $order_ids);
			});

			$result->leftJoin('conf_billing_orders', function ($join) use ($order_ids) {
				$join->on('conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id')
					->whereIn('conf_billing_orders.id', $order_ids);
			});
		}

		if (isset($formInput['type']) && $formInput['type'] == "unassigned-speakers" && isset($formInput['agenda_id']) && $formInput['agenda_id']) {
			$assign_speakers = \App\Models\EventAgendaSpeaker::where('agenda_id', $formInput['agenda_id'])->where('event_id', $formInput['event_id'])->pluck('attendee_id');
			if(count($assign_speakers) > 0) {
				$result->whereNotIn('conf_attendees.id', $assign_speakers->toArray());
			}
		}

		if (isset($formInput['action']) && $formInput['action']) {
			$result->where('conf_billing_orders.status', $formInput['action']);
		}

		//Attendee Order
		if (isset($formInput['sort_by']) && $formInput['sort_by'] == "program_id" && isset($formInput['speaker']) && $formInput['speaker'] == 1) {
			$result->leftJoin('conf_event_agenda_speakers', 'conf_event_agenda_speakers.attendee_id', '=', 'conf_event_attendees.attendee_id');
			$result->leftJoin('conf_agenda_info', function ($join) use ($formInput) {
				$join->on('conf_event_agenda_speakers.agenda_id', '=', 'conf_agenda_info.agenda_id')
					->where('conf_agenda_info.name', '=', 'topic')
					->where('conf_agenda_info.languages_id', '=', $formInput['language_id']);
			});
			$result->orderBy('conf_agenda_info.value', $formInput['order_by']);
		} else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "status")) {
			$result->orderBy('conf_billing_orders.' . $formInput['sort_by'], $formInput['order_by']);
		} else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && $formInput['sort_by'] == "created_at")) {
			$result->orderBy('conf_event_attendees.' . $formInput['sort_by'], $formInput['order_by']);
		} else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'] && in_array($formInput['sort_by'], ['company_name', 'department','title']))) {
            $result->leftJoin('conf_attendees_info', function ($join) use ($formInput) {
                $join->on('conf_attendees_info.attendee_id', '=', 'conf_attendees.id')
                    ->where('conf_attendees_info.name', '=', $formInput['sort_by'])
                    ->where('conf_attendees_info.languages_id', '=', $formInput['language_id']);
            });
			$result->orderBy('conf_attendees_info.value', $formInput['order_by']);
		} else if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('conf_attendees.first_name', 'ASC');
		}

		//Attendee Info
		$result->with(['info' => function ($query) use ($formInput) {
			return $query->where(function ($query) {
				return $query->where('name', '=', 'title')
					->orWhere('name', '=', 'company_name')
					->orWhere('name', '=', 'network_group')
					->orWhere('name', '=', 'table_number')
					->orWhere('name', '=', 'phone')
					->orWhere('name', '=', 'website')
					->orWhere('name', '=', 'allow_vote')
					->orWhere('name', '=', 'ask_to_apeak')
					->orWhere('name', '=', 'initial')
					->orWhere('name', '=', 'delegate_number')
					->orWhere('name', '=', 'department')
					->orWhere('name', '=', 'allow_gallery');
			})->where('languages_id', '=', $formInput['language_id']);
		}, 'event' => function ($q) use ($formInput) {
			return $q->where('event_id', $formInput['event_id']);
		}]);

		$result->groupby('conf_attendees.id');

		$attendees = $result->select('conf_billing_orders.status as order_status', 'conf_billing_orders.id as order_id')->paginate($formInput['limit'])->toArray();

		//Attendee Detail/Groups
		foreach ($attendees['data'] as $key => $row) {
			$response = array();
			$response = readArrayKey($row, $response, 'info');

			if (isset($response['phone']) && $response['phone']) {
				$phone = explode("-", $response['phone']);
				if (count($phone) > 1) {
					$response['calling_code'] = $phone[0];
					$response['phone'] = $phone[1];
				}
			}

			$groups = array();
			$assigned = \App\Models\EventAttendeeGroup::where('attendee_id', '=', $row['id'])
				->with(['group' => function ($query) use ($formInput) {
					$query->where('event_id', '=', $formInput['event_id']);
				}, 'group.info', 'group.parent.info'])
				->get()
				->toArray();
			if (count($assigned ?? []) > 0) {
				$container = array();
				foreach ($assigned as $i => $group) {
					$group = $group['group'];
					if ($group['event_id'] == $formInput['event_id']) {
						$container[$i][$group['info']['name']] = $group['info']['value'];
						$container[$i]['parent'] = $group['parent']['info']['value'];
						$i++;
					}
				}
				$groups[] = $container;
			}

			//For speakers
			if (isset($formInput['speaker']) && $formInput['speaker'] == 1) {
				$program = array();
				$attached_program = $this->attachedProgram($row['id'], $formInput['event_id'], $formInput['language_id']);
				foreach ($attached_program as $prg) {
					if (isset($prg->program->info)) {
						foreach ($prg->program->info as $info) {
							$program[$info->name] = $info->value;
						}
						$row['program_id'][] = $prg->agenda_id;
					}
					$row['program_detail'][] = $program;
				}
			}

			$row['group_detail'] = $groups;
			$row['attendee_detail'] = $response;
			$attendees['data'][$key] = $row;
			$attendees['data'][$key]['created_at'] = \Carbon\Carbon::parse($row['pivot']['created_at'])->format('d/m/y');
			$attendees['data'][$key]['order_status'] = strtoupper($row['order_status']);
		}

		return $attendees;
	}

    public function wizardListing($formInput)
    {
        //Event Attendees
        $result = \App\Models\Event::find($formInput['event_id'])->attendees();

        //fetch attendees as speaker
        if (isset($formInput['speaker']) && $formInput['speaker'] == 1) $result->where('speaker', 1);

        //fetch attendees for specific date
        if (isset($formInput['created_at']) && $formInput['created_at']) $result->whereDate('conf_event_attendees.created_at', $formInput['created_at']);

        //Filter date range
        if (isset($formInput['fromDate']) && $formInput['fromDate']) {
            $result->whereDate('conf_event_attendees.created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
        }
        if (isset($formInput['toDate']) && $formInput['toDate']) {
            $result->whereDate('conf_event_attendees.created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
        }

        //search
        $result->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_attendees.id')
            ->where('conf_attendees_info.languages_id', '=', $formInput['language_id']);
        if(isset($formInput['query']) && $formInput['query']){
            $search=$formInput['query'];
            $filter=!empty($formInput['sort_by'])?$formInput['sort_by']:'first_name';
            $result->where(function ($query) use ($search, $filter) {

                if ($filter == 'first_name') {
                    return $query->where(\DB::raw('CONCAT(conf_attendees.first_name, " ", conf_attendees.last_name)'), 'LIKE', '%' . trim($search) . '%');
                }

                if ($filter == 'email') {
                    return $query->where('conf_attendees.email', 'like', '%' . $search . '%');
                }
                if ($filter == 'company_name') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'company_name')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }

                if ($filter == 'delegate') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'delegate_number')
                            ->where('conf_attendees_info.value', '=', trim($search));
                    });
                }

                if ($filter == 'department') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'department')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }

                if ($filter == 'title') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'title')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }

                if ($filter == 'network_group') {
                    return $query->where(function ($query) use ($search) {
                        return $query->where('conf_attendees_info.name', '=', 'network_group')
                            ->where('conf_attendees_info.value', 'LIKE', '%' . trim($search) . '%');
                    });
                }


            });
        }

        //Active orders
        $order_ids = \App\Models\BillingOrder::where('event_id', $formInput['event_id'])->where('is_archive', 0)->where('conf_billing_orders.is_waitinglist', '=', '0')->currentOrder()->pluck('id');

        if (isset($formInput['type']) && $formInput['type'] == "registration-sign-ups") {
            $result->join('conf_billing_order_attendees', function ($join) use ($order_ids) {
                $join->on('conf_billing_order_attendees.attendee_id', '=', 'conf_attendees.id')
                    ->whereIn('conf_billing_order_attendees.order_id', $order_ids);
            });

            $result->join('conf_billing_orders', function ($join) use ($order_ids) {
                $join->on('conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id')
                    ->whereIn('conf_billing_orders.id', $order_ids);
            });
        } else {
            $result->leftJoin('conf_billing_order_attendees', function ($join) use ($order_ids) {
                $join->on('conf_billing_order_attendees.attendee_id', '=', 'conf_attendees.id')
                    ->whereIn('conf_billing_order_attendees.order_id', $order_ids);
            });

            $result->leftJoin('conf_billing_orders', function ($join) use ($order_ids) {
                $join->on('conf_billing_orders.id', '=', 'conf_billing_order_attendees.order_id')
                    ->whereIn('conf_billing_orders.id', $order_ids);
            });
        }

        if (isset($formInput['type']) && $formInput['type'] == "unassigned-speakers" && isset($formInput['agenda_id']) && $formInput['agenda_id']) {
            $assign_speakers = \App\Models\EventAgendaSpeaker::where('agenda_id', $formInput['agenda_id'])->where('event_id', $formInput['event_id'])->pluck('attendee_id');
            if(count($assign_speakers) > 0) {
                $result->whereNotIn('conf_attendees.id', $assign_speakers->toArray());
            }
        }

        if (isset($formInput['action']) && $formInput['action']) {
            $result->where('conf_billing_orders.status', $formInput['action']);
        }

        //Attendee Order
        if (isset($formInput['sort_by']) && $formInput['sort_by'] == "program_id" && isset($formInput['speaker']) && $formInput['speaker'] == 1) {
            $result->leftJoin('conf_event_agenda_speakers', 'conf_event_agenda_speakers.attendee_id', '=', 'conf_event_attendees.attendee_id');
            $result->leftJoin('conf_agenda_info', function ($join) use ($formInput) {
                $join->on('conf_event_agenda_speakers.agenda_id', '=', 'conf_agenda_info.agenda_id')
                    ->where('conf_agenda_info.name', '=', 'topic')
                    ->where('conf_agenda_info.languages_id', '=', $formInput['language_id']);
            });
            $result->orderBy('conf_agenda_info.value', $formInput['order_by']);
        }

        //Attendee Info
        $result->with(['info' => function ($query) use ($formInput) {
            return $query->where(function ($query) {
                return $query->where('name', '=', 'title')
                    ->orWhere('name', '=', 'company_name')
                    ->orWhere('name', '=', 'network_group')
                    ->orWhere('name', '=', 'table_number')
                    ->orWhere('name', '=', 'phone')
                    ->orWhere('name', '=', 'website')
                    ->orWhere('name', '=', 'allow_vote')
                    ->orWhere('name', '=', 'ask_to_apeak')
                    ->orWhere('name', '=', 'gdpr')
                    ->orWhere('name', '=', 'initial')
                    ->orWhere('name', '=', 'delegate_number')
                    ->orWhere('name', '=', 'department')
                    ->orWhere('name', '=', 'allow_gallery');
            })->where('languages_id', '=', $formInput['language_id']);
        }, 'event' => function($q) use($formInput){
            return $q->where('event_id', $formInput['event_id']);
        }]);

        $result->groupby('conf_attendees.id');

        $attendees = $result->select('conf_billing_orders.status as order_status', 'conf_billing_orders.id as order_id')->paginate($formInput['limit'])->toArray();

        //Attendee Detail/Groups
        foreach ($attendees['data'] as $key => $row) {
            $response = array();
            $response = readArrayKey($row, $response, 'info');

            if (isset($response['phone']) && $response['phone']) {
                $phone = explode("-", $response['phone']);
                if (count($phone) > 1) {
                    $response['calling_code'] = $phone[0];
                    $response['phone'] = $phone[1];
                }
            }

            $groups = array();
            $assigned = \App\Models\EventAttendeeGroup::where('attendee_id', '=', $row['id'])
                ->with(['group' => function ($query) use ($formInput) {
                    $query->where('event_id', '=', $formInput['event_id']);
                }, 'group.info', 'group.parent.info'])
                ->get()
                ->toArray();
            if (count($assigned ?? []) > 0) {
                $container = array();
                foreach ($assigned as $i => $group) {
                    $group = $group['group'];
                    if ($group['event_id'] == $formInput['event_id']) {
                        $container[$i][$group['info']['name']] = $group['info']['value'];
                        $container[$i]['parent'] = $group['parent']['info']['value'];
                        $i++;
                    }
                }
                $groups[] = $container;
            }

            //For speakers
            if (isset($formInput['speaker']) && $formInput['speaker'] == 1) {
                $program = array();
                $attached_program = $this->attachedProgram($row['id'], $formInput['event_id'], $formInput['language_id']);
                foreach($attached_program as $prg) {
                    if (isset($prg->program->info)) {
                        foreach ($prg->program->info as $info) {
                            $program[$info->name] = $info->value;
                        }
                        $row['program_id'][] = $prg->agenda_id;
                    }
                    $row['program_detail'][] = $program;
                }
            }

            $row['group_detail'] = $groups;
            $row['attendee_detail'] = $response;
            $attendees['data'][$key] = $row;
            $attendees['data'][$key]['created_at'] = \Carbon\Carbon::parse($row['pivot']['created_at'])->format('d/m/y');
            $attendees['data'][$key]['order_status'] = strtoupper($row['order_status']);
        }

        return $attendees;
    }
	/**
	 *Attendee attached program
	 *
	 * @param int
	 * @param int
	 * @param int
	 */
	public function attachedProgram($id, $event_id, $language_id)
	{
		return \App\Models\EventAgendaSpeaker::where('event_id', $event_id)
			->where('attendee_id', '=', $id)
			->with(['program.info' => function ($query) use ($language_id) {
				return $query->where('languages_id', $language_id);
			}])->get();
	}

	/*
    * attendee import settings
    */

	static public function getImportSettings()
	{
		$settings = array(
			'fields' => array(
				'initial' => array(
					'field' => 'initial',
					'label' => 'Initial',
					'type' => 'string',
					'required' => false
				),
				'first_name' => array(
					'field' => 'first_name',
					'label' => 'First Name',
					'type' => 'string',
					'required' => true
				),
				'last_name' => array(
					'field' => 'last_name',
					'label' => 'Last Name',
					'type' => 'string',
					'required' => false
				),
				'title' => array(
					'field' => 'title',
					'label' => 'Title',
					'type' => 'string',
					'required' => false
				),
				'company_name' => array(
					'field' => 'company_name',
					'label' => 'Company Name',
					'type' => 'string',
					'required' => false
				),
				'about' => array(
					'field' => 'about',
					'label' => 'About Me',
					'type' => 'string',
					'required' => false
				),
				'industry' => array(
					'field' => 'industry',
					'label' => 'Industry',
					'type' => 'string',
					'required' => false
				),
				'email' => array(
					'field' => 'email',
					'label' => 'Email',
					'type' => 'string',
					'required' => true
				),
				'website' => array(
					'field' => 'website',
					'label' => 'Website',
					'type' => 'string',
					'required' => false
				),
				'facebook' => array(
					'field' => 'facebook',
					'label' => 'Facebook',
					'type' => 'string',
					'required' => false
				),
				'twitter' => array(
					'field' => 'twitter',
					'label' => 'Twitter',
					'type' => 'string',
					'required' => false
				),
				'linkedin' => array(
					'field' => 'linkedin',
					'label' => 'LinkedIn',
					'type' => 'string',
					'required' => false
				),
				'country_iso' => array(
					'field' => 'country',
					'label' => 'Country ISO',
					'type' => 'string',
					'required' => false
				),
				'organization' => array(
					'field' => 'organization',
					'label' => 'Organization',
					'type' => 'string',
					'required' => false
				),
				'jobs' => array(
					'field' => 'jobs',
					'label' => 'Job Tasks',
					'type' => 'string',
					'required' => false
				),
				'interests' => array(
					'field' => 'interests',
					'label' => 'Interests',
					'type' => 'string',
					'required' => false
				),
				'age' => array(
					'field' => 'age',
					'label' => 'Age',
					'type' => 'integer',
					'required' => false
				),
				'gender' => array(
					'field' => 'gender',
					'label' => 'Gender',
					'type' => 'string',
					'required' => false
				),
				'country_code' => array(
					'field' => 'country_code',
					'label' => 'Country code',
					'type' => 'integer',
					'required' => false
				),
				'phone' => array(
					'field' => 'phone',
					'label' => 'Phone',
					'type' => 'integer',
					'required' => false
				),
				'allow_vote' => array(
					'field' => 'allow_vote',
					'label' => 'Voting Permissions',
					'type' => 'booleans',
					'required' => false
				),
				/*'id' =>array(
                    'field' => 'id',
                    'label' => 'Id',
                    'type' => 'integer',
                    'required' => false
                ),*/
				'group_id' => array(
					'field' => 'group_id',
					'label' => 'Group Id',
					'type' => 'list',
					'required' => false
				),
				'organizer_id' => array(
					'field' => 'organizer_id',
					'label' => 'Organizer Id',
					'type' => 'integer',
					'required' => false
				),
				'department' => array(
					'field' => 'department',
					'label' => 'Department',
					'type' => 'string',
					'required' => false
				),
				'custom_field_id' => array(
					'field' => 'custom_field_id',
					'label' => 'Drop Down',
					'type' => 'string',
					'required' => false
				),
				'allow_gallery' => array(
					'field' => 'allow_gallery',
					'label' => 'Image Gallery',
					'type' => 'booleans',
					'required' => false
				),
				'ask_to_apeak' => array(
					'field' => 'ask_to_apeak',
					'label' => 'Ask to Speak',
					'type' => 'booleans',
					'required' => false
				),'gdpr' => array(
					'field' => 'gdpr',
					'label' => 'GDPR',
					'type' => 'booleans',
					'required' => false
				),
				'network_group' => array(
					'field' => 'network_group',
					'label' => 'Network Group',
					'type' => 'string',
					'required' => false
				),
				'delegate_number' => array(
					'field' => 'delegate_number',
					'label' => 'Delegate Number',
					'type' => 'string',
					'required' => false
				),
				'table_number' => array(
					'field' => 'table_number',
					'label' => 'Table Number',
					'type' => 'string',
					'required' => false
				),
				'FIRST_NAME_PASSPORT' => array(
					'field' => 'FIRST_NAME_PASSPORT',
					'label' => 'First Name (Passport)',
					'type' => 'string',
					'required' => false
				),
				'LAST_NAME_PASSPORT' => array(
					'field' => 'LAST_NAME_PASSPORT',
					'label' => 'Last Name (Passport)',
					'type' => 'string',
					'required' => false
				),
				'BIRTHDAY_YEAR' => array(
					'field' => 'BIRTHDAY_YEAR',
					'label' => 'Date of Birth',
					'type' => 'string',
					'required' => false
				),
				'EMPLOYMENT_DATE' => array(
					'field' => 'EMPLOYMENT_DATE',
					'label' => 'Employment Date',
					'type' => 'string',
					'required' => false
				),
				'SPOKEN_LANGUAGE' => array(
					'field' => 'SPOKEN_LANGUAGE',
					'label' => 'Languages',
					'type' => 'string',
					'required' => false
				),
				'ss_number' => array(
					'field' => 'ss_number',
					'label' => 'Social security number',
					'type' => 'string',
					'required' => false
				),
				'attendee_type_id' => array(
					'field' => 'attendee_type_id',
					'label' => 'Attendee type id',
					'type' => 'string',
					'required' => false
				),
				'attendee_type' => array(
					'field' => 'attendee_type',
					'label' => 'Attendee type',
					'type' => 'string',
					'required' => false
				),
				'type_resource' => array(
					'field' => 'type_resource',
					'label' => 'Type resource',
					'type' => 'string',
					'required' => false
				),
				'allow_my_document' => array(
					'field' => 'allow_my_document',
					'label' => 'Allow document',
					'type' => 'string',
					'required' => false
				)

			),
			'add_url' => '_admin/attendee/create',
			'list_url' => '_admin/attendee',
			'export_url' => '_admin/attendee/export'
		);

		return $settings;
	}

	/**
	 *attendee export setting
	 *
	 * @param array
	 */
	static public function getExportSetting($formInput)
	{
		$labels = \App\Models\EventSiteText::where('event_id', $formInput['event_id'])
			->where('parent_id', '=', '0')
			->where('module_alias', '=', 'attendees')
			->with(['info' => function ($query) use ($formInput) {
				return $query->where('languages_id', $formInput['language_id']);
			}])
			->with(['children' => function ($query) {
				return $query->orderBy('constant_order');
			}, 'children.childrenInfo' => function ($query) use ($formInput) {
				return $query->where('languages_id', $formInput['language_id']);
			}])
			->orderBy('section_order')->get();
		$attendeeLabels = [];

		foreach ($labels['children'] as $row) {
			if (count($row['children_info'] ?? []) > 0) {
				foreach ($row['children_info'] as $val) {
					$attendeeLabels[$row['alias']] = $val['value'];
				}
			}
		}

		$settings = array(
			'fields' => array(
				'initial' => array(
					'field' => 'initial',
					'label' => 'Initial',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'first_name' => array(
					'field' => 'first_name',
					'label' => 'First Name',
					'type' => 'string',
					'required' => true
				),
				'last_name' => array(
					'field' => 'last_name',
					'label' => 'Last Name',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'title' => array(
					'field' => 'title',
					'label' => 'Title',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_TITLE'
				),
				'company_name' => array(
					'field' => 'company_name',
					'label' => 'Company Name',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_COMPANY_NAME'
				),
				'about' => array(
					'field' => 'about',
					'label' => 'About Me',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_ABOUT'
				),
				'industry' => array(
					'field' => 'industry',
					'label' => 'Industry',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_INDUSTRY'
				),
				'email' => array(
					'field' => 'email',
					'label' => 'Email',
					'type' => 'string',
					'required' => true,
					'alias' => ''
				),
				'website' => array(
					'field' => 'website',
					'label' => 'Website',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'facebook' => array(
					'field' => 'facebook',
					'label' => 'Facebook',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'twitter' => array(
					'field' => 'twitter',
					'label' => 'Twitter',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'linkedin' => array(
					'field' => 'linkedin',
					'label' => 'LinkedIn',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'country_iso' => array(
					'field' => 'country',
					'label' => 'Country ISO',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_COUNTRY'
				),
				'organization' => array(
					'field' => 'organization',
					'label' => 'Organization',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_ORGANIZATION'
				),
				'jobs' => array(
					'field' => 'jobs',
					'label' => 'Job Tasks',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_JOB_TASKS'
				),
				'interests' => array(
					'field' => 'interests',
					'label' => 'Interests',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_INTERESTS'
				),
				'age' => array(
					'field' => 'age',
					'label' => 'Age',
					'type' => 'integer',
					'required' => false,
					'alias' => ''
				),
				'gender' => array(
					'field' => 'gender',
					'label' => 'Gender',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'country_code' => array(
					'field' => 'country_code',
					'label' => 'Country code',
					'type' => 'integer',
					'required' => false,
					'alias' => ''
				),
				'phone' => array(
					'field' => 'phone',
					'label' => 'Phone',
					'type' => 'integer',
					'required' => false,
					'alias' => 'ATTENDEE_PHONE'
				),
				'allow_vote' => array(
					'field' => 'allow_vote',
					'label' => 'Voting Permissions',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'group_id' => array(
					'field' => 'group_id',
					'label' => 'Group Id',
					'type' => 'list',
					'required' => false,
					'alias' => 'ATTENDEE_LIST_BY_GROUP'
				),
				'organizer_id' => array(
					'field' => 'organizer_id',
					'label' => 'Organizer Id',
					'type' => 'integer',
					'required' => false,
					'alias' => ''
				),
				'department' => array(
					'field' => 'department',
					'label' => 'Department',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_DEPARTMENT'
				),
				'custom_field_id' => array(
					'field' => 'custom_field_id',
					'label' => 'Custom Field',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'allow_gallery' => array(
					'field' => 'allow_gallery',
					'label' => 'Image Gallery',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'ask_to_apeak' => array(
					'field' => 'ask_to_apeak',
					'label' => 'Ask to speak',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),'gdpr' => array(
                    'field' => 'gdpr',
                    'label' => 'GDPR',
                    'type' => 'booleans',
                    'required' => false
                ),
				'network_group' => array(
					'field' => 'network_group',
					'label' => 'Network Group',
					'type' => 'string',
					'required' => false,
					'alias' => 'GENERAL_NETWORK_GROUP'
				),
				'delegate_number' => array(
					'field' => 'delegate_number',
					'label' => 'Delegate Number',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_DELEGATE_NUMBER'
				),
				'table_number' => array(
					'field' => 'table_number',
					'label' => 'Table Number',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_TABLE_NUMBER'
				),
				'FIRST_NAME_PASSPORT' => array(
					'field' => 'FIRST_NAME_PASSPORT',
					'label' => 'First Name (Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'LAST_NAME_PASSPORT' => array(
					'field' => 'LAST_NAME_PASSPORT',
					'label' => 'Last Name (Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'BIRTHDAY_YEAR' => array(
					'field' => 'BIRTHDAY_YEAR',
					'label' => 'Date of Birth',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'place_of_birth' => array(
					'field' => 'place_of_birth',
					'label' => 'Place of birth(Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PLACE_OF_BIRTH'
				),
				'passport_no' => array(
					'field' => 'passport_no',
					'label' => 'Passport no',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PASSPORT_NO'
				),
				'date_of_issue_passport' => array(
					'field' => 'date_of_issue_passport',
					'label' => 'Date of issue(Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PASSPORT_ISSUE_DATE'
				),
				'date_of_expiry_passport' => array(
					'field' => 'date_of_expiry_passport',
					'label' => 'Date of expiry(Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PASSPORT_EXPIRY_DATE'
				),
				'EMPLOYMENT_DATE' => array(
					'field' => 'EMPLOYMENT_DATE',
					'label' => 'Employment Date',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'SPOKEN_LANGUAGE' => array(
					'field' => 'SPOKEN_LANGUAGE',
					'label' => 'Languages',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'ss_number' => array(
					'field' => 'ss_number',
					'label' => 'Social security number',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'gdpr' => array(
					'field' => 'gdpr',
					'label' => 'GDPR',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'attendee_type_id' => array(
					'field' => 'attendee_type_id',
					'label' => 'Attendee type id',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'attendee_type' => array(
					'field' => 'attendee_type',
					'label' => 'Attendee type',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'type_resource' => array(
					'field' => 'type_resource',
					'label' => 'Type Resource',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'allow_my_document' => array(
					'field' => 'allow_my_document',
					'label' => 'Allow Document',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'private_house_number' => array(
					'field' => 'private_house_number',
					'label' => 'Private house number',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_HOUSE_NUMBER'
				),
				'private_street' => array(
					'field' => 'private_street',
					'label' => 'Private street',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_STREET'
				),
				'private_post_code' => array(
					'field' => 'private_post_code',
					'label' => 'Private post code',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_POST_CODE'
				),
				'private_city' => array(
					'field' => 'private_city',
					'label' => 'Private city',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_CITY'
				),
				'private_country' => array(
					'field' => 'private_country',
					'label' => 'Private country',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_COUNTRY'
				),
				'attendee_id' => array(
					'field' => 'attendee_id',
					'label' => 'Attendee ID',
					'type' => 'string',
					'required' => true
				)

			)
		);

		foreach ($settings['fields'] as $key => $field) {
			if (isset($attendeeLabels[$field['alias']])) {
				$settings['fields'][$key]['label'] = $attendeeLabels[$field['alias']];
			}
		}

		return $settings;
	}

	/**
	 * validate import content for attendee
	 * @param array
	 * @param array
	 * @param array
	 * @param string
	 */
	public function import($formInput, $mapping, $data, $import_type = "new")
	{
		$result = array();
		$result['new'] = array();
		$result['duplicate'] = array();
		$result['error'] = array();
		$settings = self::getImportSettings();
		$organizer_id = organizer_id();
		$event_id = $formInput['event_id'];
		$language_id = $formInput['language_id'];
		$attendee_import = 'create';
		$email_index = array_search('email', $mapping);

		if ($import_type == 'new') {
			foreach ($data as $key => $values) {
				$info_fields = array();
				$attendee_type_id = 0;
				$allow_vote = '0';
				$allow_gallery = '0';
				$ask_to_apeak = '0';
				$gdpr = '0';
				$type_resource = '0';
				$allow_my_document = '0';
				$att_groups = '';
				$record = array();
				$error = "";
				$valid = true;
				foreach ($values as $index => $val) {
					if (isset($mapping[$index]) && $mapping[$index]) {
						$val = trim($val);
						if ($mapping[$index] == 'country') {
							$country_id = \App\Models\Country::where('code_2', trim($val))->value('id');
							$record[$mapping[$index]] = $country_id;
						} else if ($mapping[$index] == 'country_code') {
							$record['calling_code'] = trim($val);
						} elseif (trim($mapping[$index]) == 'phone') {
							$record[$mapping[$index]] = trim($val);
						} else if (trim($mapping[$index]) == 'ss_number') {
							$record[$mapping[$index]] = md5(trim($val));
						} else if ($mapping[$index] == 'attendee_type_id') {
							$attendee_type_id = trim($val);
						} elseif ($mapping[$index] == 'group_id') {
							$att_groups = trim($val);
						} else if ($mapping[$index] == 'allow_vote') {
							$allow_vote = trim($val);
						} else if ($mapping[$index] == 'allow_gallery') {
							$allow_gallery = trim($val);
						} else if ($mapping[$index] == 'ask_to_apeak') {
							$ask_to_apeak = trim($val);
						}else if ($mapping[$index] == 'gdpr') {
                            $gdpr = trim($val);
						} else if ($mapping[$index] == 'type_resource') {
							$type_resource = trim($val);
						} else if ($mapping[$index] == 'allow_my_document') {
							$allow_my_document = trim($val);
						}

						if (isset($settings['fields'][$mapping[$index]]['type'])) {
							if ($settings['fields'][$mapping[$index]]['type'] == 'string' && gettype($val) != 'string') {
								$info_fields[] = array('name' => $mapping[$index], 'value' => $val);
								$error = sprintf(__('messages.import_incorrect_format'), str_replace('_', '', $mapping[$index]));
								$valid = false;
								break;
							} elseif ($settings['fields'][$mapping[$index]]['type'] == 'integer' && $val != '') {
								$integ = (int) $val;
								if ($integ == 0) {
									$error = sprintf(__('messages.import_incorrect_format'), str_replace('_', '', $mapping[$index]));
									$valid = false;
									break;
								}
							} elseif ($settings['fields'][$mapping[$index]]['type'] == 'booleans' && $val != '') {
								if ($val != 0 && $val != 1) {
									$error = sprintf(__('messages.import_incorrect_format'), str_replace('_', '', $mapping[$index]));
									$valid = false;
									break;
								}
							} elseif ($settings['fields'][$mapping[$index]]['type'] == 'date' && verifyDate($val) != '1') {
								$error = sprintf(__('messages.import_incorrect_format'), str_replace('_', '', $mapping[$index]));
								$valid = false;
								break;
							} elseif ($settings['fields'][$mapping[$index]]['type'] == 'time' && verifyTime($val)) {
								$error = sprintf(__('messages.import_incorrect_format'), str_replace('_', '', $mapping[$index]));
								$valid = false;
								break;
							} elseif ($settings['fields'][$mapping[$index]]['type'] == 'list' && $val != '') {
								$list_data = explode(';', $val);
								foreach ($list_data as $list) {
									$list = (int) $list;
									if ($list == 0) {
										$error = sprintf(__('messages.import_incorrect_format'), str_replace('_', '', $mapping[$index]));
										$valid = false;
										break;
									}
								}
							}
						}

						if (trim($att_groups)) {
							$group_ids = explode(";", trim($att_groups));
							$groups = \App\Models\EventGroup::whereIn('id', $group_ids)->where('event_id', '=', $event_id)->count();
							if ($groups != count($group_ids)) {
								$error = sprintf(__('messages.import_group_not_found'), $mapping[$index], $val);
								$valid = false;
								break;
							} else {
								$groups_ids = explode(";", trim($att_groups));
								$record['group_id'] = $groups_ids;
							}
						}

						if ($attendee_type_id > 0 && $mapping[$index] == 'attendee_type_id') {
							$attendee_types = \App\Models\EventAttendeeType::where('id', $attendee_type_id)->where('event_id', $event_id)->count();
							if ($attendee_types == 0) {
								$error = __('messages.import_attendee_type_not_found');
								$valid = false;
							}
						}

						if (isset($settings['fields'][$mapping[$index]]['required']) && $settings['fields'][$mapping[$index]]['required'] == 1 && trim($val) == '') {
							$error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
							$valid = false;
							break;
						}

						if ($mapping[$index] == 'id') {
							$count = \App\Models\Attendee::where('id', $val)->count();
							if ($count > 0) {
								$error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
								$result['duplicate'][] = $values;
								$valid = false;
								break;
							}
						}

						if ($mapping[$index] == 'country' && trim($val) != '') {
							$count = \App\Models\Country::where('code_2', $val)->count();
							if ($count == 0) {
								$error = sprintf(__('messages.import_field_invalid_value'), $mapping[$index], $val);
								$valid = false;
								break;
							}
						}

						if ($mapping[$index] == 'ss_number' && trim($val) != '') {
							$ss_number = $val;
							if (strlen($ss_number) == 10) {
								// $ss_number = md5($val);
								// $attendee = \App\Models\Attendee::where('ss_number', $ss_number)->where('email', '!=',  $values[$email_index])->where('organizer_id', $organizer_id)->first();
								// if ($attendee) {
								// 	$error = __('messages.import_cpr_already_taken');
								// 	$record[$mapping[$index]] = '';
								// 	$valid = false;
								// }
							} elseif (strlen($ss_number) == 11) {
								$ss_number = str_replace('-', '', $ss_number);
								if (strlen($ss_number) == 10) {
									// $ss_number = md5($ss_number);
									// $attendee = \App\Models\Attendee::where('ss_number', $ss_number)->where('email', '!=',  $values[$email_index])->where('organizer_id', $organizer_id)->first();
									// if ($attendee) {
									// 	$error = __('messages.import_cpr_already_taken');
									// 	$record[$mapping[$index]] = '';
									// 	$valid = false;
									// }
								} else {
									$error = __('messages.import_cpr_invalid');
									$record[$mapping[$index]] = '';
									$valid = false;
								}
							} else {
								$error = __('messages.import_cpr_invalid');
								$record[$mapping[$index]] = '';
								$valid = false;
							}
						}

						if ($mapping[$index] == 'email') {
							if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
								$attendee = \App\Models\Attendee::where('organizer_id', $organizer_id)->where('email', trim($val))->first();
								if ($attendee) {
									$attendee_import = 'update';
									$event_attendee = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', $event_id)->count();
									//Assign attendee to event
									if ($event_attendee == 0) {
										//check if attendee as a speaker
										$speaker = (isset($formInput['speaker']) && $formInput['speaker'] ? '1' : '0');

										//gdpr setting
										$gdpr_accepted = \App\Models\SpeakerSetting::where('event_id', $event_id)->value('gdpr_accepted');
										$gdpr = ($gdpr_accepted ? 1 : 0);

										$model = new \App\Models\EventAttendee(array('event_id' => $event_id, 'default_language_id' => $language_id, 'status' => 1, 'attendee_type' => $attendee_type_id, 'email_sent' => 0, 'sms_sent' => 0, 'login_yet' => 0, 'verification_id' => '', 'allow_vote' => $allow_vote, 'allow_gallery' => $allow_gallery, 'ask_to_apeak' => $ask_to_apeak, 'type_resource' => $type_resource, 'allow_my_document' => $allow_my_document, 'speaker' => $speaker, 'gdpr' => $gdpr));

										$attendee->event()->save($model);
									}
								}
							} else {
								$error = __('messages.import_invalid_email');
								$valid = false;
								break;
							}
						}

						if ($mapping[$index] == '-1') { //Do not map this field
							$val = '';
						}

						if (!in_array($mapping[$index], ['country'])) $record[$mapping[$index]] = $val;
					}
				}

				if ($valid) {
					//store attendee
					$record['event_id'] = $event_id;
					$record['language_id'] = $language_id;
					$record['attendee_type_id'] = $attendee_type_id;
					if ($attendee_import == 'create') {
						$this->store($record);
					} elseif ($attendee_import == 'update') {
						$attendee_import = 'create';
						$attendee = \App\Models\Attendee::where('organizer_id', $organizer_id)->where('email', $record['email'])->first();
						if ($attendee) {
							$this->edit($record, $attendee);
						}
					}
					$result['new'][] = $record;
				} else {
					$record["error"] = $error;
					$result['error'][] = $record;
				}
			}
		}

		return $result;
	}

	/**
	 *Attendee invitations
	 *
	 * @param array
	 */

	public function invitations($formInput, $count = false)
	{
		$assigned_attendees = $this->getAssignedAttendees($formInput['event_id']);

		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('not_send', '0');

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		if (isset($formInput['status']) && $formInput['status']) {
			$result->where('status', $formInput['status']);
		} else {
			$result->where('status', '0');
		}

		if (isset($formInput['is_resend']) && $formInput['is_resend']) {
			$result->where('is_resend', $formInput['is_resend']);
		}

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->where(function ($query) use ($formInput) {
				return $query->where(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('email', 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('phone', 'LIKE', '%' . $formInput['query'] . '%');
			});
		}

		$result->whereNotIn('email', $assigned_attendees);

		//Attendee Order
		if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('first_name', 'ASC');
		}

		if ($count == true) {
			return $result->get()->count();
		} else {
			$attendees = $result->paginate($formInput['limit'])->toArray();

			foreach ($attendees['data'] as $key => $row) {
				$attendees['data'][$key] = $row;
				if (isset($row['phone']) && $row['phone']) {
					$phone = explode("-", $row['phone']);
					if (count($phone) > 1) {
						$attendees['data'][$key]['calling_code'] = $phone[0];
						$attendees['data'][$key]['phone'] = $phone[1];
					}
				}
			}

			return $attendees;
		}
	}

	/**
	 *fetch assigned attendees to event
	 *
	 * @param int
	 */
	public function getAssignedAttendees($event_id)
	{
		$attendees = \App\Models\EventAttendee::join('conf_attendees', 'conf_event_attendees.attendee_id', '=', 'conf_attendees.id')
			->where('conf_event_attendees.event_id', $event_id)
			->select('conf_attendees.email')
			->get()
			->toArray();

		return $attendees = Arr::flatten($attendees);
	}

	/**
	 *create attendee invitation
	 *
	 * @param array
	 */

	public function create_invitation($formInput)
	{
		if (isset($formInput['phone']) && isset($formInput['calling_code']) && $formInput['phone'])
			$formInput['phone'] = '+' . ltrim($formInput['calling_code'], '+') . '-' . $formInput['phone'];

		if (isset($formInput['ss_number']) && $formInput['ss_number']) {
			$formInput['ss_number'] = md5(str_replace('-', '', $formInput['ss_number']));
		} else {
			$formInput['ss_number'] = $formInput['old_ss_number'];
		}

		$allow_vote = 0;
		$ask_to_speak = 0;

		if (isset($formInput['allow_vote']) && in_array($formInput['allow_vote'], array('1', 'x', 'X'))) {
			$allow_vote = 1;
		}

		if (isset($formInput['ask_to_speak']) && in_array($formInput['ask_to_speak'], array('1', 'x', 'X'))) {
			$ask_to_speak = 1;
		}

		return \App\Models\AttendeeInvite::create([
			"event_id" => $formInput['event_id'],
			"organizer_id" => organizer_id(),
			"first_name" => $formInput['first_name'],
			"last_name" => ($formInput['last_name'] ? $formInput['last_name'] : ''),
			"email" => $formInput['email'],
			"phone" => ($formInput['phone'] ? $formInput['phone'] : ''),
			"ss_number" => ($formInput['ss_number'] ? $formInput['ss_number'] : NULL),
			"allow_vote" => $allow_vote,
			'ask_to_speak' => $ask_to_speak
		]);
	}

	/**
	 *update attendee invitation
	 *
	 * @param array
	 * @param int
	 */

	public function update_invitation($formInput, $id)
	{
		if (isset($formInput['phone']) && isset($formInput['calling_code']) && $formInput['phone'])
			$formInput['phone'] = '+' . ltrim($formInput['calling_code'], '+') . '-' . $formInput['phone'];

		if (isset($formInput['ss_number']) && $formInput['ss_number']) {
			$formInput['ss_number'] = md5(str_replace('-', '', $formInput['ss_number']));
		}

		$record = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])
			->where('organizer_id', organizer_id())
			->where('id', $id)
			->first();
		$record->first_name = $formInput['first_name'];
		$record->last_name = ($formInput['last_name'] ? $formInput['last_name'] : '');
		$record->email = $formInput['email'];
		$record->phone = ($formInput['phone'] ? $formInput['phone'] : '');
		if (isset($formInput['ss_number']) && !empty($formInput['ss_number'])) {
			$record->ss_number = $formInput['ss_number'];
		}
		$record->allow_vote = $formInput['allow_vote'];
		$record->ask_to_speak = $formInput['ask_to_speak'];
		return $record->save();
	}

	/**
	 *delete attendee invitation
	 *
	 * @param array
	 */

	public function destroy_invitation($formInput, $id)
	{
		$query = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])
			->where('organizer_id', organizer_id());

		if (is_array($id)) {
			$query->whereIn('id', $id);
		} else if (is_numeric($id)) {
			$query->where('id', $id);
		}

		if (isset($formInput['module']) && $formInput['module'] == "add_reg") {
			$query->where('status', 0);
		} else if (isset($formInput['module']) && $formInput['module'] == "not_registered") {
			$query->where('is_attending', 0)->where('status', 1);
		} else if (isset($formInput['module']) && $formInput['module'] == "not_attendees_list") {
			$query->where('is_attending', 1)->where('status', 1);
		}

		$query->delete();
	}

	/*
    * Import & Export Settings For Attendee Invitations
    */

	static public function getImportSettingsInvites($is_export = false)
	{
		if ($is_export == true) {
			$settings = array(
				'fields' => array(
					'first_name' => array(
						'field' => 'first_name',
						'label' => 'First Name',
						'type' => 'string',
						'required' => true
					),
					'last_name' => array(
						'field' => 'last_name',
						'label' => 'Last Name',
						'type' => 'string',
						'required' => false
					),
					'phone_number' => array(
						'field' => 'phone_number',
						'label' => 'Phone number',
						'type' => 'integer',
						'required' => false
					),
					'email' => array(
						'field' => 'email',
						'label' => 'Email',
						'type' => 'string',
						'required' => false
					),
					'ss_number' => array(
						'field' => 'ss_number',
						'label' => 'Social security number',
						'type' => 'string',
						'required' => false
					),
					'allow_vote' => array(
						'field' => 'allow_vote',
						'label' => 'Allow vote',
						'type' => 'string',
						'required' => false
					),
					'ask_to_speak' => array(
						'field' => 'ask_to_speak',
						'label' => 'Ask to speak',
						'type' => 'string',
						'required' => false
					)
				)
			);
		} else {
			$settings = array(
				'fields' => array(
					'first_name' => array(
						'field' => 'first_name',
						'label' => 'First Name',
						'type' => 'string',
						'required' => true
					),
					'last_name' => array(
						'field' => 'last_name',
						'label' => 'Last Name',
						'type' => 'string',
						'required' => false
					),
					'phone_number' => array(
						'field' => 'phone_number',
						'label' => 'Phone number',
						'type' => 'integer',
						'required' => false
					),
					'email' => array(
						'field' => 'email',
						'label' => 'Email',
						'type' => 'string',
						'required' => false
					),
					'ss_number' => array(
						'field' => 'ss_number',
						'label' => 'Social security number',
						'type' => 'string',
						'required' => false
					),
					'allow_vote' => array(
						'field' => 'allow_vote',
						'label' => 'Allow vote',
						'type' => 'string',
						'required' => false
					),
					'ask_to_speak' => array(
						'field' => 'ask_to_speak',
						'label' => 'Ask to speak',
						'type' => 'string',
						'required' => false
					)
				)
			);
		}
		return $settings;
	}

	/*
	* Import & Export functions
	*/

	static public function getImportSettingsNotRegistered($is_export = false)
	{
		if ($is_export = true) {
			$settings = array(
				'fields' => array(
					'first_name' => array(
						'field' => 'first_name',
						'label' => 'First Name',
						'type' => 'string',
						'required' => true
					),
					'last_name' => array(
						'field' => 'last_name',
						'label' => 'Last Name',
						'type' => 'string',
						'required' => false
					),
					'phone_number' => array(
						'field' => 'phone',
						'label' => 'Phone number',
						'type' => 'integer',
						'required' => false
					),
					'email' => array(
						'field' => 'email',
						'label' => 'Email',
						'type' => 'string',
						'required' => false
					)
				)
			);
		} else {
			$settings = array(
				'fields' => array(
					'first_name' => array(
						'field' => 'first_name',
						'label' => 'First Name',
						'type' => 'string',
						'required' => true
					),
					'last_name' => array(
						'field' => 'last_name',
						'label' => 'Last Name',
						'type' => 'string',
						'required' => false
					),
					'phone_number' => array(
						'field' => 'phone_number',
						'label' => 'Phone number',
						'type' => 'integer',
						'required' => false
					),
					'email' => array(
						'field' => 'email',
						'label' => 'Email',
						'type' => 'string',
						'required' => false
					)
				)
			);
		}

		return $settings;
	}

	/*
	* Export functions
	*/
	static public function getImportNotAttendingSettings()
	{

		$settings = array(
			'fields' => array(
				'first_name' => array(
					'field' => 'first_name',
					'label' => 'First name*',
					'required' => false
				),
				'last_name' => array(
					'field' => 'last_name',
					'label' => 'Last name',
					'required' => false
				),
				'phone' => array(
					'field' => 'phone',
					'label' => 'Phone',
					'required' => true
				),
				'Email' => array(
					'field' => 'email',
					'label' => 'Email*',
					'required' => false
				)
			)
		);

		return $settings;
	}

	/**
	 * import content for attendee invitations
	 * @param array
	 * @param array
	 * @param array
	 * @param string
	 */
	public function importAttendeeInvitations($formInput, $mapping, $data, $import_type = "new")
	{
		$result = array();
		$result['new'] = array();
		$result['duplicate'] = array();
		$result['error'] = array();
		$organizer_id = organizer_id();
		$event_id = $formInput['event_id'];
		$emailIndex = array_search("email", $mapping);
		$firstNameIndex = array_search("first_name", $mapping);
		$lastNameIndex = array_search("last_name", $mapping);
		if ($import_type == 'new') {
			foreach ($data as $key => $values) {
				$record = array();
				$error = "";
				$valid = true;
				foreach ($values as $index => $val) {
					if (isset($mapping[$index]) && $mapping[$index]) {
						$record['event_id'] = $event_id;
						$record['organizer_id'] = $organizer_id;
						$record[$mapping[$index]] = $val;

						if ($mapping[$index] == 'country_code') {
							$record['calling_code'] = trim($val);
						}

						if ($mapping[$index] == 'email') {
							if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
								$count = \App\Models\AttendeeInvite::where('organizer_id', $organizer_id)->where('event_id', $event_id)->where('email', $record['email'])->count();
								if ($count > 0) {
									$error = sprintf(__('messages.import_field_already_exist'), $val);
									$result['duplicate'][] = $record;
									$valid = false;
									break 1;
								} else {
									if (isset($record['phone_number']) && $record['phone_number'] != '') {
										$record['phone'] = $record['phone_number'];
									}
									//$result['new'][] = $values;
									$record['first_name'] = addslashes(trim($record['first_name']));
									$record['last_name'] = addslashes(trim($record['last_name']));
									$record['email'] = $record['email'];
								}
							} else {
								$error = __('messages.import_invalid_email');
								$valid = false;
								break;
							}
						}

						if ($mapping[$index] == 'ss_number' && trim($val) != '') {
							$ss_number = $val;
							if (strlen($ss_number) == 10) {
								$ss_number = md5($val);
								$attendee = AttendeeInvite::where('ss_number', $ss_number)->where('event_id', $event_id)->where('email', '!=',  $record['email'])->where('organizer_id', $organizer_id)->first();
								if ($attendee) {
									$error = __('messages.import_cpr_already_taken');
									$record[$mapping[$index]] = '';
									$valid = false;
									break;
								}
							} elseif (strlen($ss_number) == 11) {
								$ss_number = str_replace('-', '', $ss_number);
								if (strlen($ss_number) == 10) {
									$ss_number = md5($ss_number);
									$attendee = \App\Models\Attendee::where('ss_number', $ss_number)->where('email', '!=',  $record['email'])->where('organizer_id', $organizer_id)->first();
									if ($attendee) {
										$error = __('messages.import_cpr_already_taken');
										$record[$mapping[$index]] = '';
										$valid = false;
										break;
									}
								} else {
									$error = __('messages.import_cpr_invalid');
									$record[$mapping[$index]] = '';
									$valid = false;
									break;
								}
							} else {
								$error = __('messages.import_cpr_invalid');
								$record[$mapping[$index]] = '';
								$valid = false;
								break;
							}
						}

						if ($mapping[$index] == '-1') { //Do not map this field
							$val = '';
						}
					}
				}

				if ($valid) {
					//create invitation
					$this->create_invitation($record);

					$result['new'][] = $record;
				} else {
					$record["error"] = $error;
					$result['error'][] = $record;
				}
			}
		}

		return $result;
	}

	/**
	 *update attendee invitation status
	 *
	 * @param array
	 */

	public function update_invitation_status($formInput)
	{
		return \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])
			->where('organizer_id', organizer_id())
			->whereIn('id', $formInput['ids'])
			->update([
				"status" => 1
			]);
	}

	/**
	 *invite attendees
	 *
	 * @param array
	 */

	public function invite_attendees($formInput)
	{
		if ($formInput['invite_type'] == 'registration_invite' || $formInput['invite_type'] == 'registration_invite_reminder') {
			return \App\Models\AttendeeInvite::whereIn('id', $formInput['ids'])->get();
		} else {
			return Attendee::join('conf_attendees_info', function ($join) use ($formInput) {
				$join->on('conf_attendees.id', '=', 'conf_attendees_info.attendee_id')
					->where('conf_attendees_info.languages_id', $formInput['language_id']);
			})
				->join('conf_event_attendees AS event_attendee', function ($join) use ($formInput) {
					$join->on('conf_attendees.id', '=', 'event_attendee.attendee_id')
						->where('event_attendee.event_id', $formInput['event_id'])
						->whereNull('event_attendee.deleted_at');
				})
				->select(array(
					'conf_attendees.*', 'conf_attendees_info.value as phone', 'event_attendee.email_sent',
					'event_attendee.sms_sent'
				))
				->whereNull('conf_attendees.deleted_at')
				->whereIn('conf_attendees.id', $formInput['ids'])
				->orderBy('conf_attendees.first_name', 'ASC')
				->groupBy('conf_attendees.id')
				->get();
		}
	}

	/**
	 *invitation template
	 *
	 * @param array
	 */
	public function invitation_template($formInput)
	{
		$template_value = $sms_value = "";
		
		$event = \App\Models\Event::where('id', $formInput['event_id'])->first();

		$event_name = $event->name;

		$event_url = cdn('/event/' . $event->url);

		if(in_array($formInput['invite_type'], ['registration_invite', 'all_invites', 'reg_all_reinvites', 'registration_invite_reminder'])) {
			
			$aliases = self::getAliasByInviteType($formInput);

			$templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $formInput['event_id'], 'alias'=> $aliases['email'] , 'registration_form_id' => $formInput['registration_form_id'], 'language_id' => $formInput['language_id']]);

			$template_value = $templateData->template;
	
			$sms_value = '';

		} else {

			$template = $this->getInviteTemplate($formInput);

			if (isset($template['email_template']['info'])) {
				foreach ($template['email_template']['info'] as $row) {
					if ($row['name'] == 'template') {
						$template_value = $row['value'];
					}
				}
			}

			if (isset($template['sms_template']['info'])) {
				foreach ($template['sms_template']['info'] as $row) {
					if ($row['name'] == 'template') {
						$sms_value = $row['value'];
					}
				}
			}

		}
		
		$event_setting  = get_event_branding($formInput['event_id']);

		if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
			$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
		} else {
			$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
		}

		$logo = '<img src="' . $src . '" width="150" />';

		$organizer = organizer_info();

		if ($formInput['invite_type'] == 'invoice_reminder_email')
			$organizer_name = stripslashes($organizer->first_name);
		else
			$organizer_name = $event->organizer_name;

		$att_ids = $formInput['ids'];

		if ($formInput['invite_type'] == 'all_invites') {
			$attendees_invite = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('status', '=', '0')
				->where('not_send', '=', '0')->first();
			$attendees_invite = $attendees_invite[0];
		} elseif ($formInput['invite_type'] == 'all_reinvites' || $formInput['invite_type'] == 'reg_all_reinvites') {
			$attendees_invite = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('status', '=', '1')
				->where('not_send', '=', '0')->first();
			\App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->update([
				'is_resend' => 0
			]);
		} elseif ($formInput['invite_type'] == 'not_send_all_invites') {
			$eventAttendees = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('app_invite_sent', '=', 0)->first();
			$attendees_invite['id'] = $eventAttendees->attendee_id;
		} elseif ($formInput['invite_type'] == 'resend_all_invites' || $formInput['invite_type'] == 'app_invite_reminder' || $formInput['invite_type'] == 'app_invite_reminder_all') {
			$eventAttendees = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('app_invite_sent', '=', 1)->first();
			$attendees_invite['id'] = $eventAttendees->attendee_id;
		}

		$attendee_id = (isset($att_ids[0]) && $att_ids[0] ? $att_ids[0] : $attendees_invite['id']);

		if ($formInput['invite_type'] == 'not_send_all_invites' || $formInput['invite_type'] == 'resend_all_invites' || $formInput['invite_type'] == 'app_invite_reminder' || $formInput['invite_type'] == 'app_invite_reminder_all') {
			$attendee_id = $attendees_invite['id'];
		}

		if (isset($formInput['module']) && ($formInput['module'] == 'app_invitation' || $formInput['module'] == 'billing_history' || $formInput['module'] == 'order_history' || $formInput['module'] == 'app_invitation_sent' || $formInput['module'] == 'app_invitation_not_sent' || $formInput['module'] == 'not_send_all_invites' || $formInput['module'] == 'resend_all_invites')) {
			$attendee = \App\Models\Attendee::where('id', $attendee_id)->with('info')->first();
			if ($attendee) {
				$attendee = $attendee->toArray();
				$info = readArrayKey($attendee, [], 'info');
				$attendee = array_merge($info, $attendee);
			}
		} else {
			$attendee = \App\Models\AttendeeInvite::where('id', $attendee_id)->first();
			if (!$attendee) {
				$attendee = \App\Models\AttendeeInvite::where('id', $attendees_invite['id'])->first();
			}
		}

		$unsubscribe_attendee_url = cdn('/_admin/attendee/unsubscribe_attendee/' . $formInput['event_id'] . '/' . $attendee_id);

		$registerLinkLabel = 'click here to register.';

		//template dynamic field replacement
		$registerLink = '<a href="' . $event_url . '/autoregister/' . base64_encode($formInput['event_id'] . '-' . $attendee_id) . '" >' . $registerLinkLabel . '</a>';

		$gender = (isset($attendee['gender']) ? $attendee['gender'] : '');

		$template_value = str_replace("{event_logo}", stripslashes($logo), $template_value);
		$template_value = str_replace("{event_name}", stripslashes($event_name), $template_value);
		$template_value = str_replace("{app_link}", stripslashes($event_url), $template_value);
		$template_value = str_replace("{login_link}", stripslashes($event_url), $template_value);
		$template_value = str_replace("{register_link}", stripslashes($registerLink), $template_value);
		$template_value = str_replace("{initial}", stripslashes($attendee['initial']), $template_value);
		$template_value = str_replace("{attendee_name}", stripslashes($attendee['first_name'] . ' ' . $attendee['last_name']), $template_value);
		$template_value = str_replace("{first_name}", stripslashes($attendee['first_name']), $template_value);
		$template_value = str_replace("{last_name}", stripslashes($attendee['last_name']), $template_value);
		$template_value = str_replace("{attendee_email}", stripslashes($attendee['email']), $template_value);
		$template_value = str_replace("{event_organizer_name}", stripslashes($organizer_name), $template_value);
		$template_value = str_replace("{attendee_initial}", stripslashes($attendee['initial']), $template_value);
		$template_value = str_replace("{gender}", stripslashes($gender), $template_value);
		$url = cdn('assets/attendees/' . $attendee['image']);
		$template_value = str_replace("{attendee_image}", '<img src="' . $url . '" width="150px" height="150px"/>', $template_value);
		$template_value = str_replace("{attendee_compnay_name}", stripslashes($attendee['company_name']), $template_value);
		$template_value = str_replace("{attendee_industry}", stripslashes($attendee['industry']), $template_value);
		$template_value = str_replace("{attendee_department}", stripslashes($attendee['department']), $template_value);
		$template_value = str_replace("{title}", stripslashes($attendee['title']), $template_value);
		$template_value = str_replace("{about}", stripslashes($attendee['about']), $template_value);
		$template_value = str_replace("{website}", stripslashes($attendee['website_protocol'] . $attendee['website']), $template_value);
		$template_value = str_replace("{facebook}", stripslashes($attendee['facebook_protocol'] . $attendee['facebook']), $template_value);
		$template_value = str_replace("{twitter}", stripslashes($attendee['twitter_protocol'] . $attendee['twitter']), $template_value);
		$template_value = str_replace("{linkedin}", stripslashes($attendee['linkedin_protocol'] . $attendee['linkedin']), $template_value);
		$template_value = str_replace("{eventsite_URL}", cdn('/event/' . $event->url . '/detail'), $template_value);
		$template_value = str_replace("{unsubscribe_attendee}", '<a href="' . $unsubscribe_attendee_url . '">	Unsubscribe Attendee</a>', $template_value);
		$template_value = str_replace("{unsubscribe_attendee_link}", $unsubscribe_attendee_url, $template_value);
		$template_value = str_replace("{unsubscribe_attendee_label}", 'Unsubscribe Attendee', $template_value);
		$badge_URL = cdn('/_badges/printEmailBadges/' . $attendee_id . '/' . $formInput['event_id']);
		$template_value = str_replace("{badge_template}", "" . $badge_URL, $template_value);
		$template_value = str_replace("{not_attending}", '<a href="' . cdn('/event/' . $event->url . '/detail/attendee_not_attending?id=' . $attendee['id'] . '&event_id=' . $formInput['event_id']) . '">Unsubscribe</a>', $template_value);
		$template_value = str_replace("{unsubscribe_attendee_link}", cdn('/event/' . $event->url . '/detail/attendee_not_attending?id=' . $attendee['id'] . '&event_id=' . $formInput['event_id']), $template_value);
		$template_value = str_replace("{unsubscribe_attendee_label}", 'Unsubscribe', $template_value);

		$organizer_id = organizer_id();

		$checkinoutURL = $this->checkInOutRepository->generateURlShortner([
			'attendee_id' => $attendee_id,
			'event_id' => $formInput['event_id'],
			'organizer_id' => $organizer_id,
			'event_url' => $event_url,
		]);

		$qrCodeImgSrc = cdn("api/QrCode?chs=200x200&chl=" . urlencode($checkinoutURL));

		$qrCodeImg = '<img style="position: inherit;max-width: inherit;max-height: inherit;float: none;" width="200" height="200" src="' . $qrCodeImgSrc . '">';

		$template_value = str_replace("{qr_code}", $qrCodeImg, $template_value);

		//Attendees Group
		$group_names = array();
		$event_groups = \App\Models\EventGroup::where('event_id', $formInput['event_id'])->get()->toArray();
		foreach ($event_groups as $group) {
			$attendee_group_assigned = \App\Models\EventAttendeeGroup::where('attendee_id', $attendee['id'])->where('group_id', $group['id'])->first();
			if ($attendee_group_assigned) {
				$group_info = \App\Models\EventGroupInfo::where('group_id', $attendee_group_assigned->group_id)->where('languages_id', $formInput['language_id'])->first();
				if (!array_key_exists($attendee_group_assigned->group_id, $group_names)) {
					$group_names[$attendee_group_assigned->group_id] = $group_info->value;
				}
			}
		}

		$group_names = implode('<br>', $group_names);

		$template_value = str_replace("{attendee_groups}", '<br>' . $group_names . '<br>', $template_value);

		if ($formInput['invite_type'] == 'resend_all_invites' || $formInput['invite_type'] == 'app_invite_reminder_all') {
			\App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('app_invite_sent', 1)->update([
				"app_invite_sent" => 2
			]);
		}

		$register_attendees_email = \App\Models\Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->pluck('conf_attendees.email');

		$emails = ($register_attendees_email ? $register_attendees_email->toArray() : []);

		if ($formInput['invite_type'] == 'all_invites') {
			$total_attendees = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('status', '=', '0')->where('not_send', '=', '0')->whereNotIn('email', $emails)->count();
		} elseif ($formInput['invite_type'] == 'all_reinvites' || $formInput['invite_type'] == 'reg_all_reinvites') {
			$total_attendees = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('status', '=', '1')->where('is_attending', '=', '0')->where('not_send', '=', '0')->whereNotIn('email', $emails)->count();
		} elseif ($formInput['invite_type'] == 'not_send_all_invites' || $formInput['invite_type'] == 'resend_all_invites' || $formInput['invite_type'] == 'app_invite_reminder' || $formInput['invite_type'] == 'app_invite_reminder_all') {
			if ($formInput['invite_type'] == 'not_send_all_invites') {
				$total_attendees = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('app_invite_sent', '=', 0)->count();
			} elseif ($formInput['invite_type'] == 'app_invite_reminder') {
				$total_attendees = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->whereIn('attendee_id', $formInput['ids'])->where('app_invite_sent', '=', 1)->count();
			} elseif ($formInput['invite_type'] == 'resend_all_invites' || $formInput['invite_type'] == 'app_invite_reminder_all') {
				$total_attendees = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('app_invite_sent', '=', 2)->count();
			}
		} else {
			$total_attendees = count($formInput['ids']  ?? []);
		}

		$style = email_background_color($formInput['event_id']);

		if (isset($style['css'])) $template_value = $style['css'] . $template_value;

		return [
			'template' => (in_array($formInput['action'], ['send_by_email', 'resend_by_email', 'send_by_email_all', 'reminder_by_email', 'reminder_by_email_all', 'send_by_sms_email', 'send_by_email_sms_all']) ?  $template_value :  ''),
			'sms' => (in_array($formInput['action'], ['send_by_sms', 'send_by_sms_all', 'reminder_by_sms', 'reminder_by_sms_all', 'send_by_sms_email', 'send_by_email_sms_all']) ?  $sms_value :  ''),
			'total_attendees' => $total_attendees
		];
	}
	
	/**
	 * getAliasByInviteType
	 *
	 * @param  mixed $formInput
	 * @return void
	 */
	public static function getAliasByInviteType($formInput)
	{
		if ($formInput['invite_type'] == 'registration_invite') {
			$email_template_alias = 'registration_invite';
			$sms_template_alias = 'attendee_invite_sms';
		} else if ($formInput['invite_type'] == 'all_invites' || $formInput['invite_type'] == 'reg_all_reinvites') {
			$email_template_alias = 'registration_invite';
			$sms_template_alias = 'attendee_invite_sms';
		} else if ($formInput['invite_type'] == 'registration_invite_reminder') {
			$email_template_alias = 'attendee_reminder_email';
			$sms_template_alias = 'attendee_reminder_sms';
		} else if ($formInput['invite_type'] == 'all_reinvites' || $formInput['invite_type'] == 'app_invite_reminder' || $formInput['invite_type'] == 'app_invite_reminder_all') {
			$email_template_alias = 'attendee_reminder_email';
			$sms_template_alias = 'attendee_reminder_sms';
		} else if ($formInput['invite_type'] == 'invoice_reminder_email') {
			$email_template_alias = 'invoice_reminder_email';
			$sms_template_alias = 'invoice_reminder_sms';
		} else {
			$email_template_alias = 'attendee';
			$sms_template_alias = 'attendee_sms';
		}

		return [
			'email' => $email_template_alias,
			'sms' => $sms_template_alias
		];
	}

	/**
	 *fetch invitation template
	 *
	 * @param array
	 */
	public function getInviteTemplate($formInput)
	{
		if ($formInput['invite_type'] == 'registration_invite') {
			$email_template_alias = 'registration_invite';
			$sms_template_alias = 'attendee_invite_sms';
		} else if ($formInput['invite_type'] == 'all_invites' || $formInput['invite_type'] == 'reg_all_reinvites') {
			$email_template_alias = 'registration_invite';
			$sms_template_alias = 'attendee_invite_sms';
		} else if ($formInput['invite_type'] == 'registration_invite_reminder') {
			$email_template_alias = 'attendee_reminder_email';
			$sms_template_alias = 'attendee_reminder_sms';
		} else if ($formInput['invite_type'] == 'all_reinvites' || $formInput['invite_type'] == 'app_invite_reminder' || $formInput['invite_type'] == 'app_invite_reminder_all') {
			$email_template_alias = 'attendee_reminder_email';
			$sms_template_alias = 'attendee_reminder_sms';
		} else if ($formInput['invite_type'] == 'invoice_reminder_email') {
			$email_template_alias = 'invoice_reminder_email';
			$sms_template_alias = 'invoice_reminder_sms';
		} else {
			$email_template_alias = 'attendee';
			$sms_template_alias = 'attendee_sms';
		}

		$email_template = \App\Models\EventEmailTemplate::where('event_id', $formInput['event_id'])
			->where('alias', '=', $email_template_alias)->where('type', '=', 'email')
			->with(['info' => function ($q) use ($formInput) {
				$q->where('languages_id', $formInput['language_id']);
			}])->first();

		$sms_template = \App\Models\EventEmailTemplate::where('event_id', $formInput['event_id'])
			->where('alias', '=', $sms_template_alias)->where('type', '=', 'sms')
			->with(['info' => function ($q) use ($formInput) {
				$q->where('languages_id', $formInput['language_id']);
			}])->first();

		return array('email_template' => $email_template, 'sms_template' => $sms_template);
	}

	/**
	 *send invitation
	 *
	 * @param array
	 */
	public function send_invitation($formInput)
	{
		$registered_attendees = Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select(array('conf_attendees.email'))
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->get();

		$emails = array();

		foreach ($registered_attendees as $key => $attendees) {
			$emails[$key] = $attendees->email;
		}

		if ($formInput['invite_type'] == 'all_invites') {
			$page = $formInput['ids'];
			$limit = 50;
			$offset = 0;
			$all_invites = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('registration_form_id', (int)$formInput['registration_form_id'])
				->where('status', '=', '0')->where('not_send', '=', '0')->whereNotIn('email', $emails)->offset($offset)->limit($limit)->get()->toArray();
		} elseif ($formInput['invite_type'] == 'all_reinvites') {
			$page = $formInput['ids'];
			$limit = 50;
			$offset = 0;
			$all_invites = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('registration_form_id', (int)$formInput['registration_form_id'])
				->where('status', '=', '1')->where('is_attending', '=', '0')->where('not_send', '=', '0')->where('is_resend', '=', '0')->whereNotIn('email', $emails)->offset($offset)->limit($limit)->get()->toArray();
		} elseif ($formInput['invite_type'] == 'reg_all_reinvites') {
			$page = $formInput['ids'];
			$limit = 50;
			$offset = 0;
			$all_invites = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])->where('registration_form_id', (int)$formInput['registration_form_id'])
				->where('status', '=', '1')->where('not_send', '=', '0')->where('is_resend', '=', '0')->where('is_attending', '=', '0')->whereNotIn('email', $emails)->offset($offset)->limit($limit)->get()->toArray();
		} elseif ($formInput['invite_type'] == 'not_send_all_invites') {
			$page = $formInput['ids'];
			$limit = 50;
			$offset = 0;
			$eventAttendees = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('app_invite_sent', '=', 0)->whereNull('deleted_at')->offset($offset)->limit($limit)->get()->toArray();
		} elseif ($formInput['invite_type'] == 'resend_all_invites' || $formInput['invite_type'] == 'app_invite_reminder_all') {
			$page = $formInput['ids'];
			$limit = 50;
			$offset = 0;
			$eventAttendees = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('app_invite_sent', '=', 2)->whereNull('deleted_at')->offset($offset)->limit($limit)->get()->toArray();
		}

		$all_invite_ids = array();

		if (isset($all_invites) && $all_invites) {
			foreach ($all_invites as $val) {
				$all_invite_ids[] = $val['id'];
			}
		}

		$all_attendee_ids = array();

		if (isset($eventAttendees) && $eventAttendees) {
			foreach ($eventAttendees as $val2) {
				$all_attendee_ids[] = $val2['attendee_id'];
			}
		}

		if (count($all_invite_ids ?? []) > 0) {
			$ids = $all_invite_ids;
		} elseif (count($all_attendee_ids ?? []) > 0) {
			$ids = $all_attendee_ids;
		} else {
			if (count($formInput['ids'] ?? []) > 50) {
				$ids = array_slice($formInput['ids'], 0, 50, true);
				$remaing_ids = array_slice($formInput['ids'], 50, count($formInput['ids'] ?? []), true);
			} else {
				$ids = $formInput['ids'];
			}
		}

		$formInput['ids'] = $ids;

		$this->sendInvitationEmail($formInput);

		return array(
			'ids' => (isset($remaing_ids) && count($remaing_ids  ?? []) > 0 ? array_values($remaing_ids) : false)
		);
	}

	public function sendInvitationEmail($formInput)
	{
		$date = date('Y-m-d H:i:s');

		foreach ($formInput['ids'] as $id) {
			$attendee_invite = \App\Models\AttendeeInvite::find($id);
			if ($formInput['action'] == 'send_by_email' || $formInput['action'] == 'send_by_email_all' || $formInput['action'] == 'send_by_email_sms_all' || $formInput['action'] == 'send_by_sms_email') {
				if ($formInput['invite_type'] == 'registration_invite' || $formInput['invite_type'] == 'all_invites') {
					$this->sendAttendeeInviteEmail($formInput, $attendee_invite);
				} elseif ($formInput['invite_type'] == 'reg_all_reinvites') {
					$this->sendAttendeeInviteEmail($formInput, $attendee_invite);
					\App\Models\AttendeeInvite::where('id', $id)->where('event_id', $formInput['event_id'])->update([
						"is_resend" => 1
					]);
				} elseif ($formInput['invite_type'] == 'registration_invite_reminder' || $formInput['invite_type'] == 'all_reinvites') {
					$this->sendAttendeeInviteReminderEmail($formInput, $attendee_invite);
					\App\Models\AttendeeInvite::where('id', $id)->where('event_id', $formInput['event_id'])->update([
						"is_resend" => 1
					]);
				} elseif ($formInput['invite_type'] == 'invoice_reminder_email') {
					$this->sendAttendeeInvoinceReminderEmail($formInput, $id);
				} elseif ($formInput['invite_type'] == 'app_invite' || $formInput['invite_type'] == 'not_send_all_invites' || $formInput['invite_type'] == 'resend_all_invites') {
					$attendee = \App\Models\Attendee::where('id', $id)->with('info')->first();
					if ($attendee) {
						$attendee = $attendee->toArray();
						$info = readArrayKey($attendee, [], 'info');
						$attendee = array_merge($info, $attendee);
					}

					//Maintain email History
					$invite_history = new \App\Models\EventAttendeeEmailHistory();
					$invite_history->event_id = $formInput['event_id'];
					$invite_history->attendee_id = $id;
					$invite_history->email_date = $date;
					$invite_history->save();

					// App Invite Email Log
					$attendee_invite_app = new \App\Models\EventAttendeeAppInviteLog();
					$attendee_invite_app->event_id = $formInput['event_id'];
					$attendee_invite_app->attendee_id = $id;
					$attendee_invite_app->email_sent = '1';
					$attendee_invite_app->sms_sent = '0';
					$attendee_invite_app->email_date = $date;
					$attendee_invite_app->save();

					\App\Models\EventAttendee::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->update([
						"app_invite_sent" => 1
					]);

					$this->sendAppInviteEmail($formInput, $attendee);
				}

				if ($formInput['invite_type'] == 'registration_invite' || $formInput['invite_type'] == 'all_invites') {
					//update invite record
					$attendee_invite->status = 1;
					$attendee_invite->save();

					//Maintain email History
					$invite_history = new \App\Models\EventAttendeeEmailHistoryInvite();
					$invite_history->event_id = $formInput['event_id'];
					$invite_history->email = $attendee_invite->email;
					$invite_history->email_date = $date;
					$invite_history->save();

					//Insert Into Log
					$invite_log = new \App\Models\AttendeeInviteLog();
					$invite_log->organizer_id = organizer_id();
					$invite_log->event_id = $formInput['event_id'];
					$invite_log->first_name = $attendee_invite->first_name;
					$invite_log->last_name = $attendee_invite->last_name;
					$invite_log->email = $attendee_invite->email;
					$invite_log->phone = $attendee_invite->phone;
					$invite_log->email_sent = 1;
					$invite_log->date_sent = $date;
					$invite_log->save();
				}
			}

			if ($formInput['action'] == 'resend_by_email') {
				if ($formInput['invite_type'] == 'registration_invite' || $formInput['invite_type'] == 'all_invites') {
					$this->sendAttendeeInviteEmail($formInput, $attendee_invite);
				} elseif ($formInput['invite_type'] == 'registration_invite_reminder' || $formInput['invite_type'] == 'all_reinvites') {
					$this->sendAttendeeInviteReminderEmail($formInput, $attendee_invite);
				} elseif ($formInput['invite_type'] == 'app_invite' || $formInput['invite_type'] == 'not_send_all_invites' || $formInput['invite_type'] == 'resend_all_invites') {
					$attendee = \App\Models\Attendee::where('id', $id)->with('info')->first();
					if ($attendee) {
						$attendee = $attendee->toArray();
						$info = readArrayKey($attendee, [], 'info');
						$attendee = array_merge($info, $attendee);
					}

					//Maintain email History
					$invite_history = new \App\Models\EventAttendeeEmailHistory();
					$invite_history->event_id = $formInput['event_id'];
					$invite_history->attendee_id = $id;
					$invite_history->email_date = $date;
					$invite_history->save();
					$this->sendAppInviteEmail($formInput, $attendee);
				}

				if ($formInput['invite_type'] == 'registration_invite' || $formInput['invite_type'] == 'registration_invite_reminder' || $formInput['invite_type'] == 'all_invites' || $formInput['invite_type'] == 'all_reinvites') {
					//update invite record
					$attendee_invite->status = 1;
					$attendee_invite->save();

					//Maintain email History
					$invite_history = new \App\Models\EventAttendeeEmailHistoryInvite();
					$invite_history->event_id = $formInput['event_id'];
					$invite_history->email = $attendee_invite->email;
					$invite_history->email_date = $date;
					$invite_history->save();

					//Insert Into Log
					$invite_log = new \App\Models\AttendeeInviteLog();
					$invite_log->organizer_id = organizer_id();
					$invite_log->event_id = $formInput['event_id'];
					$invite_log->first_name = $attendee_invite->first_name;
					$invite_log->last_name = $attendee_invite->last_name;
					$invite_log->email = $attendee_invite->email;
					$invite_log->phone = $attendee_invite->phone;
					$invite_log->email_sent = 1;
					$invite_log->date_sent = $date;
					$invite_log->save();
				}
			}

			if ($formInput['action'] == 'reminder_by_email' || $formInput['action'] == 'reminder_by_email_all') {
				$attendee = \App\Models\Attendee::where('id', $id)->first();
				$this->sendAttendeeInviteReminderEmail($formInput, $attendee);
				\App\Models\EventAttendee::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->update([
					"app_invite_sent" => 1
				]);
			}

			if ($formInput['action'] == 'send_by_sms' || $formInput['action'] == 'send_by_sms_all' || $formInput['action'] == 'reminder_by_sms' || $formInput['action'] == 'reminder_by_sms_all' || $formInput['action'] == 'send_by_email_sms_all' || $formInput['action'] == 'send_by_sms_email') {
				$sms_alias = '';
				if ($formInput['invite_type'] == 'registration_invite' || $formInput['invite_type'] == 'all_invites') {
					$this->sendInviteAccountSMS($formInput, $attendee_invite, '', 'attendee_invite_sms');

					$invite_log = new \App\Models\AttendeeInviteLog();
					$invite_log->organizer_id = organizer_id();
					$invite_log->event_id = $formInput['event_id'];
					$invite_log->first_name = $attendee_invite['first_name'];
					$invite_log->last_name = $attendee_invite['last_name'];
					$invite_log->email = $attendee_invite['email'];
					$invite_log->phone = $attendee_invite['phone'];
					$invite_log->sms_sent = 1;
					$invite_log->date_sent = $date;
					$invite_log->save();

					\App\Models\AttendeeInvite::where('email', $attendee_invite['email'])->where('event_id', $formInput['event_id'])->update([
						"status" => 1
					]);
				} elseif ($formInput['invite_type'] == 'registration_invite_reminder' || $formInput['invite_type'] == 'all_reinvites') {
					$this->sendInviteAccountSMS($formInput, $attendee_invite, '', 'attendee_reminder_sms');

					$invite_log = new \App\Models\AttendeeInviteLog();
					$invite_log->organizer_id = organizer_id();
					$invite_log->event_id = $formInput['event_id'];
					$invite_log->first_name = $attendee_invite['first_name'];
					$invite_log->last_name = $attendee_invite['last_name'];
					$invite_log->email = $attendee_invite['email'];
					$invite_log->phone = $attendee_invite['phone'];
					$invite_log->sms_sent = 1;
					$invite_log->date_sent = $date;
					$invite_log->save();
				} elseif ($formInput['invite_type'] == 'app_invite' || $formInput['invite_type'] == 'not_send_all_invites' || $formInput['invite_type'] == 'resend_all_invites') {

					$attendee = \App\Models\Attendee::where('id', $id)->with('info')->first();
					if ($attendee) {
						$attendee = $attendee->toArray();
						$info = readArrayKey($attendee, [], 'info');
						$attendee = array_merge($info, $attendee);
					}

					$this->sendNewAccountSMS($formInput, $attendee, '', 'attendee_invite');

					$invite_log = new \App\Models\AttendeeInviteLog();
					$invite_log->organizer_id = organizer_id();

					$invite_log->event_id = $formInput['event_id'];
					$invite_log->first_name = $attendee['first_name'];
					$invite_log->last_name = $attendee['last_name'];
					$invite_log->email = $attendee['email'];
					if (isset($attendee['info'])) {
						foreach ($attendee['info'] as $info) {
							if ($info['name'] == 'phone') {
								$invite_log->phone = $info['value'];
							}
						}
					}
					$invite_log->sms_sent = 1;
					$invite_log->date_sent = $date;
					$invite_log->save();


					// App Invite Email Log
					$attendee_invite_app = new \App\Models\EventAttendeeAppInviteLog();
					$attendee_invite_app->event_id = $formInput['event_id'];
					$attendee_invite_app->attendee_id = $id;
					$attendee_invite_app->email_sent = '0';
					$attendee_invite_app->sms_sent = '1';

					$attendee_invite_app->email_date = $date;
					$attendee_invite_app->save();

					\App\Models\EventAttendee::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->update([
						"app_invite_sent" => 1
					]);
				} elseif ($formInput['invite_type'] == 'app_invite_reminder' || $formInput['invite_type'] == 'app_invite_reminder_all') {

					$attendee = \App\Models\Attendee::where('id', $id)->first()->toArray();

					$this->sendInviteAccountSMS($formInput, $attendee, '', 'attendee_reminder_sms');

					\App\Models\EventAttendee::where('attendee_id', $id)->where('event_id', $formInput['event_id'])->update([
						"app_invite_sent" => 1
					]);
				}
			}
		}
	}

	public function sendAttendeeInviteEmail($formInput, $record)
	{
		$event = \App\Models\Event::where('id', $formInput['event_id'])->first();

		if(in_array($formInput['invite_type'], ['registration_invite', 'all_invites', 'reg_all_reinvites', 'registration_invite_reminder'])) {
			
			$aliases = self::getAliasByInviteType($formInput);

			$templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $formInput['event_id'], 'alias'=> $aliases['email'] , 'registration_form_id' => $formInput['registration_form_id'], 'language_id' => $formInput['language_id']]);

			$template = $templateData->template;

			$subject_template = $templateData->subject;

		} else {

			$templateData = $this->getInviteTemplate($formInput);

			$subject_template = $template = $alias = "";

			if (isset($templateData['email_template']['info'])) {
				$alias = $templateData['email_template']->alias;
				foreach ($templateData['email_template']['info'] as $row) {
					if ($row['name'] == 'template') {
						$template = $row['value'];
					}
					if ($row['name'] == 'subject') {
						$subject_template = $row['value'];
					}
				}
			}

		}

		$attendee = \App\Models\Attendee::where('id', $record->id)->with('info')->first();

		if ($attendee) {
			$attendee = $attendee->toArray();
			$info = readArrayKey($attendee, [], 'info');
			$attendee = array_merge($info, $attendee);
		}

		$attendee_initial_value = (isset($attendee['initial']) ? $attendee['initial'] : '');
		
		$contents = stripslashes($template);

		$event_setting  = get_event_branding($formInput['event_id']);

		if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
			$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
		} else {
			$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
		}

		$logo = '<img src="' . $src . '" width="150" />';

		$registerLinkLabel = 'click here to register.';

		if($event->registration_form_id == 1) {
			$registration_form_id = EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($event->id, 'attendee');
			$registration_form = EventSiteSettingRepository::getRegistrationFormById(['event_id' =>  $event->id, 'id' => $record->registration_form_id ? $record->registration_form_id : $registration_form_id]);
			$registerLink = '<a href="' . config('app.reg_site_url') . '/'.$event->url. '?autoregister='.base64_encode($formInput['event_id'] . '-' . $record->id).'" >' . $registerLinkLabel . '</a>';
			$login_link =  config('app.reg_site_url')  .'/'.  $event->url;
			$not_attending_link =  config('app.reg_site_url')  .'/'.  $event->url . '/attendee_not_attending?id=' . $record->id . '&event_id=' . $event->id . '&email=' . $record->email;
			$registration_form_link = '<a href="' .config('app.reg_flow_url') .'/'.$event->url. '/attendee/manage-attendee?attendee_types='.$registration_form->type_id . '">'.$registerLinkLabel.'</a>';
		} else {
			$registerLink = '<a href="' . cdn('/event/' . $event->url . '/autoregister/' . base64_encode($formInput['event_id'] . '-' . $record->id)) . '" >' . $registerLinkLabel . '</a>';
			$login_link = cdn('/event/' . $event->url . '/detail');
			$not_attending_link = cdn('/event/' . $event->url . '/detail/attendee_not_attending?id=' . $record->id . '&event_id=' . $formInput['event_id'] . '&email=' . $record->email);
			$registration_form_link = "";
		}
		
		$subject = str_replace("{event_name}", stripslashes($event->name), $subject_template);
		$contents = str_replace("{event_logo}", stripslashes($logo), $contents);
		$contents = str_replace("{event_name}", stripslashes($event->name), $contents);
		$contents = str_replace("{initial}", stripslashes($attendee_initial_value), $contents);
		$contents = str_replace("{attendee_name}", stripslashes($record->first_name . ' ' . $record->last_name), $contents);
		$contents = str_replace("{first_name}", stripslashes($record->first_name), $contents);
		$contents = str_replace("{last_name}", stripslashes($record->last_name), $contents);
		$contents = str_replace("{attendee_email}", stripslashes($record->email), $contents);
		$contents = str_replace("{email}", stripslashes($record->email), $contents);
		$contents = str_replace("{login_link}", $login_link, $contents);
		$contents = str_replace("{register_link}", stripslashes($registerLink), $contents);
		$contents = str_replace("{eventsite_URL}", $login_link, $contents);
		$contents = str_replace("{event_organizer_name}", "" . $event->organizer_name, $contents);
		$contents = str_replace("{not_attending}", '<a style="color:#000000 !important;" href="' . $not_attending_link . '">Unsubscribe</a>', $contents);
		$contents = str_replace("{unsubscribe_attendee_link}", $not_attending_link, $contents);
		$contents = str_replace("{unsubscribe_attendee_label}", 'Unsubscribe', $contents);
		$badge_URL = cdn('/_badges/printEmailBadges/' . $record->id . '/' . $formInput['event_id']);
		$contents = str_replace("{badge_template}", "" . $badge_URL, $contents);
		$contents = str_replace("{registration_form_link}", stripslashes($registration_form_link), $contents);

		//Attendees Group
		$group_names = array();
		$event_groups = \App\Models\EventGroup::where('event_id', $formInput['event_id'])->get()->toArray();
		foreach ($event_groups as $group) {
			$attendee_group_assigned = \App\Models\EventAttendeeGroup::where('attendee_id', $record->id)->where('group_id', $group['id'])->first();
			if ($attendee_group_assigned) {
				$group_info = \App\Models\EventGroupInfo::where('group_id', $attendee_group_assigned->group_id)->where('languages_id', $formInput['language_id'])->first();
				if (!array_key_exists($attendee_group_assigned->group_id, $group_names)) {
					$group_names[$attendee_group_assigned->group_id] = $group_info->value;
				}
			}
		}
		$group_names = implode('<br>', $group_names);
		$contents = str_replace("{attendee_groups}", '<br>' . $group_names . '<br>', $contents);
		$contents = getEmailTemplate($contents, $formInput['event_id']);

		//Stats
		if (\App\Models\AttendeeInviteStats::where('organizer_id', organizer_id())->where('template_alias', $alias)->where('event_id', $formInput['event_id'])->where('email', $record->email)->count() == 0) {
			\App\Models\AttendeeInviteStats::create([
				"organizer_id" => organizer_id(),
				"template_alias" => $alias,
				"event_id" => $formInput['event_id'],
				"email" => $record->email,
			]);
		}

		//email
		$data = array();
		$data['template'] = $alias;
		$data['event_id'] = $formInput['event_id'];
		$data['subject'] = $subject;
		$data['content'] = $contents;
		$data['view'] = 'email.plain-text';
		$data['from_name'] = $event->organizer_name;
		$data['email'] = $record->email;
		$this->data['bcc'] = ['mms@eventbuizz.com'];
		\Mail::to($record->email)->send(new Email($data));
	}

	public function sendAttendeeInviteReminderEmail($formInput, $record)
	{
		$event = \App\Models\Event::where('id', $formInput['event_id'])->first();

		if(in_array($formInput['invite_type'], ['registration_invite', 'all_invites', 'reg_all_reinvites', 'registration_invite_reminder'])) {
			
			$aliases = self::getAliasByInviteType($formInput);

			$templateData = (object)TemplateRepository::getTemplateDataByAlias(['event_id' => $formInput['event_id'], 'alias'=> $aliases['email'] , 'registration_form_id' => $formInput['registration_form_id'], 'language_id' => $formInput['language_id']]);

			$template = $templateData->template;

			$subject_template = $templateData->subject;

		} else {

			$templateData = $this->getInviteTemplate($formInput);

			$subject_template = $template = "";

			if (isset($templateData['email_template']['info'])) {
				foreach ($templateData['email_template']['info'] as $row) {
					if ($row['name'] == 'template') {
						$template = $row['value'];
					}
					if ($row['name'] == 'subject') {
						$subject_template = $row['value'];
					}
				}
			}

		}

		$attendee = \App\Models\Attendee::where('id', $record->id)->with('info')->first();

		if ($attendee) {
			$attendee = $attendee->toArray();
			$info = readArrayKey($attendee, [], 'info');
			$attendee = array_merge($info, $attendee);
		}

		$attendee_initial_value = (isset($attendee['initial']) ? $attendee['initial'] : '');

		$contents = stripslashes($template);

		$event_setting  = get_event_branding($formInput['event_id']);

		if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
			$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
		} else {
			$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
		}

		$logo = '<img src="' . $src . '" width="150" />';
		
		$registerLinkLabel = 'click here to register.';
		
        if($event->registration_form_id == 1) {
			$registration_form_id = EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($event->id, 'attendee');
			$registration_form = EventSiteSettingRepository::getRegistrationFormById(['event_id' =>  $event->id, 'id' => $record->registration_form_id ? $record->registration_form_id : $registration_form_id]);
            $registerLink = '<a href="' . config('app.reg_site_url') . '/'.$event->url. '?autoregister='.base64_encode($formInput['event_id'] . '-' . $record->id).'" >' . $registerLinkLabel . '</a>';
            $not_attending_link =  config('app.reg_site_url')  .'/'.  $event->url . '/attendee_not_attending?id=' . $record->id . '&event_id=' . $formInput['event_id'] . '&email=' . $record->email;
            $login_link =  config('app.reg_site_url')  .'/'.  $event->url;
			$registration_form_link = '<a href="' .config('app.reg_flow_url') .'/'.$event->url. '/attendee/manage-attendee?attendee_types='.$registration_form->type_id . '">'.$registerLinkLabel.'</a>';
        } else {
            $registerLink = '<a href="' . cdn('/event/' . $event->url . '/autoregister/' . base64_encode($formInput['event_id'] . '-' . $record->id) ) .'" >' . $registerLinkLabel . '</a>';
			$not_attending_link =  cdn('/event/' . $event->url . '/detail/attendee_not_attending?id=' . $record->id . '&event_id=' . $formInput['event_id'] . '&email=' . $record->email) ;
        	$login_link = cdn('/event/' . $event->url . '/detail') ;
			$registration_form_link = "";
        }

		$subject = str_replace("{event_name}", stripslashes($event->name), $subject_template);
		$contents = str_replace("{attendee_name}", $record->first_name . ' ' . $record->last_name, $contents);
		$contents = str_replace("{initial}", $attendee_initial_value, $contents);
		$contents = str_replace("{first_name}", $record->first_name, $contents);
		$contents = str_replace("{last_name}", $record->last_name, $contents);
		$contents = str_replace("{attendee_email}", $record->email, $contents);
		$contents = str_replace("{event_logo}", stripslashes($logo), $contents);
		$contents = str_replace("{event_name}", stripslashes($event->name), $contents);
		$contents = str_replace("{event_organizer_name}", stripslashes($event->organizer_name), $contents);
		$contents = str_replace("{app_link}", cdn('/event/' . $event->url), $contents);
		$contents = str_replace("{login_link}", $login_link, $contents);
		$contents = str_replace("{eventsite_URL}", $login_link, $contents);
		$contents = str_replace("{not_attending}", '<a href="' . $not_attending_link . '">Unsubscribe</a>', $contents);
		$contents = str_replace("{unsubscribe_attendee_link}", $not_attending_link, $contents);
		$contents = str_replace("{unsubscribe_attendee_label}", 'Unsubscribe', $contents);
		$badge_URL = cdn('/_badges/printEmailBadges/' . $record->id . '/' . $formInput['event_id']);
		$contents = str_replace("{badge_template}", "" . $badge_URL, $contents);
		$contents = str_replace("{register_link}", stripslashes($registerLink), $contents);
		$contents = str_replace("{registration_form_link}", stripslashes($registration_form_link), $contents);

		//Attendees Group
		$group_names = array();
		$event_groups = \App\Models\EventGroup::where('event_id', $formInput['event_id'])->get()->toArray();
		foreach ($event_groups as $group) {
			$attendee_group_assigned = \App\Models\EventAttendeeGroup::where('attendee_id', $record->id)->where('group_id', $group['id'])->first();
			if ($attendee_group_assigned) {
				$group_info = \App\Models\EventGroupInfo::where('group_id', $attendee_group_assigned->group_id)->where('languages_id', $formInput['language_id'])->first();
				if (!array_key_exists($attendee_group_assigned->group_id, $group_names)) {
					$group_names[$attendee_group_assigned->group_id] = $group_info->value;
				}
			}
		}

		$group_names = implode('<br>', $group_names);

		$contents = str_replace("{attendee_groups}", '<br>' . $group_names . '<br>', $contents);

		$contents = getEmailTemplate($contents, $formInput['event_id']);

		$data = array();
		$data['subject'] = $subject;
		$data['content'] = $contents;
		$data['view'] = 'email.plain-text';
		$data['from_name'] =  $event->organizer_name;
		$this->data['bcc'] = ['mms@eventbuizz.com'];

		\Mail::to($record->email)->send(new Email($data));
		
	}

	public function sendAttendeeInvoinceReminderEmail($formInput, $attendee_id)
	{
		$attendee_order = \App\Models\BillingOrder::where('attendee_id', $attendee_id)->where('event_id', $formInput['event_id'])->select('id', 'attendee_id', 'event_id')->orderBy('id', 'DESC')->first();

		$attendee_remiders = \App\Models\InvoiceEmailReminderLog::where('attendee_id', $attendee_id)->where('event_id', $formInput['event_id'])->where('order_id', $attendee_order->id)->first();

		if ($attendee_remiders) {
			$attendee_remiders->status = 0;
			$attendee_remiders->save();
		} else {
			\App\Models\InvoiceEmailReminderLog::create([
				"event_id" => $formInput['event_id'],
				"order_id" => $attendee_order->id,
				"attendee_id" => $attendee_id,
			]);
		}
	}

	public function sendAppInviteEmail($formInput, $record)
	{
		$organizer_id = organizer_id();

		$event = \App\Models\Event::where('id', $formInput['event_id'])->first();

		$templateData = $this->getInviteTemplate($formInput);

		$subject_template = $template = $alias = "";

		if (isset($templateData['email_template']['info'])) {
			$alias = $templateData['email_template']->alias;
			foreach ($templateData['email_template']['info'] as $row) {
				if ($row['name'] == 'template') {
					$template = $row['value'];
				}
				if ($row['name'] == 'subject') {
					$subject_template = $row['value'];
				}
			}
		}

		$template = getEmailTemplate($template, $formInput['event_id']);

		$base_url = cdn('/event/' . $event->url);

		$checkinoutURL = $this->checkInOutRepository->generateURlShortner($record['id'], $event->id, $organizer_id);

		$gender = (isset($record['gender']) ? $record['gender'] : '');

		$contents = stripslashes($template);

		$event_setting  = get_event_branding($formInput['event_id']);

		if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
			$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
		} else {
			$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
		}

		$logo = '<img src="' . $src . '" width="150" />';

		$subject = str_replace("{event_name}", stripslashes($event->name), $subject_template);
		$contents = str_replace("{event_logo}", $logo, $contents);
		$contents = str_replace("{event_name}", stripslashes($event->name), $contents);
		$contents = str_replace("{attendee_initial}", stripslashes($record['initial']), $contents);
		$contents = str_replace("{attendee_name}", stripslashes($record['first_name'] . ' ' . $record['last_name']), $contents);
		$contents = str_replace("{initial}", stripslashes($record['initial']), $contents);
		$contents = str_replace("{first_name}", stripslashes($record['first_name']), $contents);
		$contents = str_replace("{last_name}", stripslashes($record['last_name']), $contents);
		$contents = str_replace("{attendee_age}", stripslashes($record['age']), $contents);
		$contents = str_replace("{attendee_email}", stripslashes($record['email']), $contents);
		$contents = str_replace("{attendee_image}", '<img src="' . (!$record['image'] ? cdn("/images/speakers/no-img.jpg") : cdn("/assets/attendees/" . $record['image'])) . '">', $contents);
		$contents = str_replace("{gender}", stripslashes($gender), $contents);
		$contents = str_replace("{email}", stripslashes($record['email']), $contents);
		//$contents = str_replace("{attendee_compnay_name}", stripslashes($company_name), $contents);
		$contents = str_replace("{attendee_organization}", stripslashes($record['organization']), $contents);
		$contents = str_replace("{attendee_department}", stripslashes($record['department']), $contents);
		$contents = str_replace("{app_link}", cdn('/event/' . $event->url), $contents);

		$contents = str_replace("{not_attending}", '<a style="color:#000000 !important;" href="' . cdn('/event/' . $event->url . '/detail/attendee_not_attending?id=' . $record['id'] . '&email=' . $record['email'] . '&type=' . $templateData['email_template']['alias'] . '&event_id=' . $event->id) . '">Unsubscribe</a>', $contents);
		$contents = str_replace("{unsubscribe_attendee_link}", cdn('/event/' . $event->url . '/detail/attendee_not_attending?id=' . $record['id'] . '&email=' . $record['email'] . '&type=' . $templateData['email_template']['alias'] . '&event_id=' . $event->id), $contents);
		$contents = str_replace("{unsubscribe_attendee_label}", 'Unsubscribe', $contents);
		//Attendees Group
		$group_names = array();
		$event_groups = \App\Models\EventGroup::where('event_id', $formInput['event_id'])->get()->toArray();
		foreach ($event_groups as $group) {
			$attendee_group_assigned = \App\Models\EventAttendeeGroup::where('attendee_id', $record['id'])->where('group_id', $group['id'])->first();
			if ($attendee_group_assigned) {
				$group_info = \App\Models\EventGroupInfo::where('group_id', $attendee_group_assigned->group_id)->where('languages_id', $formInput['language_id'])->first();
				if (!array_key_exists($attendee_group_assigned->group_id, $group_names)) {
					$group_names[$attendee_group_assigned->group_id] = $group_info->value;
				}
			}
		}
		$group_names = implode('<br>', $group_names);
		$contents = str_replace("{attendee_groups}", '<br>' . $group_names . '<br>', $contents);
		$unsubscribe_attendee_url = $base_url . '/detail/attendee/unsubscribe_attendee/' . $event->id . '/' . $record['id'];

		//$contents = str_replace("{attendee_country}", stripslashes($country_name[0]['name']), $contents);
		$contents = str_replace("{attendee_job_tasks}", stripslashes($record['jobs']), $contents);
		$contents = str_replace("{attendee_interests}", stripslashes($record['interests']), $contents);
		$contents = str_replace("{title}", stripslashes($record['title']), $contents);
		$contents = str_replace("{speaker_title}", stripslashes($record['title']), $contents);
		$contents = str_replace("{attendee_industry}", stripslashes($record['industry']), $contents);
		$contents = str_replace("{about}", stripslashes($record['about']), $contents);
		$contents = str_replace("{speaker_about}", stripslashes($record['about']), $contents);
		$contents = str_replace("{phone_number}", stripslashes($record['phone']), $contents);
		$contents = str_replace("{speaker_phone}", stripslashes($record['phone']), $contents);
		$contents = str_replace("{website}", stripslashes($record['website']), $contents);
		$contents = str_replace("{speaker_website}", stripslashes($record['website']), $contents);
		$contents = str_replace("{facebook}", stripslashes($record['facebook']), $contents);
		$contents = str_replace("{speaker_facebook}", stripslashes($record['facebook']), $contents);
		$contents = str_replace("{twitter}", stripslashes($record['twitter']), $contents);
		$contents = str_replace("{speaker_twitter}", stripslashes($record['twitter']), $contents);
		$contents = str_replace("{linkedin}", stripslashes($record['linkedin']), $contents);
		$contents = str_replace("{speaker_linkedin}", stripslashes($record['linkedin']), $contents);

		$contents = str_replace("{unsubscribe_attendee}", '<a target="#" href="' . $unsubscribe_attendee_url . '">Unsubscribe</a>', $contents);
		$contents = str_replace("{unsubscribe_attendee_link}", $unsubscribe_attendee_url, $contents);
		$contents = str_replace("{unsubscribe_attendee_label}", 'Unsubscribe', $contents);
		$contents = str_replace("{qr_code}", '<a target="#" href="' . cdn("api/QrCode?chs=200x200&chl=" . urlencode($checkinoutURL)) . '">' . '<img src="' . cdn("api/QrCode?chs=200x200&chl=" . urlencode($checkinoutURL)) . '" />' . '</a>', $contents);
		$contents = str_replace("{app_store_icon}", '<a href="https://itunes.apple.com/us/app/eventbuizz/id1086599355?ls=1&mt=8" target="_blank"><img src="' . cdn('/_admin_assets/images/apple_app.png') . '" /></a>', $contents);
		$contents = str_replace("{google_play_icon}", '<a href="https://play.google.com/store/apps/details?id=com.eventbuizz.app" target="_blank"><img src="' . cdn('/_admin_assets/images/google_app.png') . '" /></a>', $contents);
		$badge_URL = cdn('/_badges/printEmailBadges/' . $record['id'] . '/' . $event->id);
		$contents = str_replace("{badge_template}", "" . $badge_URL, $contents);

		$contents = str_replace("{event_organizer_name}", "" . $event->organizer_name, $contents);

		$checkinoutURL = $this->checkInOutRepository->generateURlShortner($record['id'], $event->id, $organizer_id);

		$qrCodeImgSrc = cdn("api/QrCode?chs=200x200&chl=" . urlencode($checkinoutURL));
		$qrCodeImg = '<img style="position: inherit;max-width: inherit;max-height: inherit;float: none;" width="200" height="200" src="' . $qrCodeImgSrc . '">';
		$contents = str_replace("{qr_code}", $qrCodeImg, $contents);

		//Stats
		if (\App\Models\AttendeeInviteStats::where('organizer_id', organizer_id())->where('template_alias', $alias)->where('event_id', $formInput['event_id'])->where('email', $record['email'])->count() == 0) {
			\App\Models\AttendeeInviteStats::create([
				"organizer_id" => organizer_id(),
				"template_alias" => $alias,
				"event_id" => $formInput['event_id'],
				"email" => $record['email'],
			]);
		}

		$data = array();
		$data['event_id'] = $event->id;
		$data['template'] = $alias;
		$data['subject'] = $subject;
		$data['content'] = $contents;
		$data['view'] = 'email.plain-text';
		$data['from_name'] =  $event->organizer_name;
		$this->data['bcc'] = ['mms@eventbuizz.com'];
		\Mail::to($record['email'])->send(new Email($data));
	}

	public function sendInviteAccountSMS($formInput, $record, $password, $type)
	{
		$template = $sms_organizer_name = "";

		$organizer_id = organizer_id();

		$event = \App\Models\Event::where('id', $formInput['event_id'])->with('info')->first();

		$sms_template = $this->getInviteTemplate($formInput);

		if (isset($sms_template['sms_template']['info'])) {
			foreach ($sms_template['sms_template']['info'] as $row) {
				if ($row['name'] == 'template') {
					$template = $row['value'];
				}
			}
		}

		foreach ($event->info as $val) {
			if ($val->name == 'sms_organizer_name') {
				$sms_organizer_name = $val['value'];
			}
		}

		if (!empty($sms_template)) {
			$sms_body = str_replace("{event_name}", stripslashes($event->name), $template);
			$sms_body = str_replace("{attendee_name}", stripslashes($record['first_name'] . ' ' . $record['last_name']), $sms_body);
			$sms_body = str_replace("{attendee_email}", stripslashes($record['email']), $sms_body);
			$sms_body = str_replace("{login_link}", cdn('/event/' . $event->url . '/detail'), $sms_body);
			$sms_body = str_replace("{event_organizer_name}", $event->organizer_name, $sms_body);

			if (isset($password) && $password != '') {
				$sms_body = str_replace("{attendee_password}", stripslashes($password), $sms_body);
			} else {
				$sms_body = str_replace("{attendee_password}", '******', $sms_body);
			}

			$sms_body = strip_tags($sms_body);

			if ($record['phone']) {
				$phone = str_replace(' ', '', (ltrim($record['phone'], '+0')));
				$phone = str_replace('-', '', $phone);
				//send SMS

				$sms_status = sendSMS($sms_body, $phone, $sms_organizer_name);

				//Maintain SMS History
				$sms_invite_history = new \App\Models\EventSmsHistory();
				$sms_invite_history->organizer_id = $organizer_id;
				$sms_invite_history->event_id = $event->id;
				$sms_invite_history->attendee_id = $record['id'];
				$sms_invite_history->email = $record['email'];
				$sms_invite_history->status = $sms_status['status'];
				$sms_invite_history->status_msg = addslashes($sms_status['status_msg']);
				$sms_invite_history->date_sent = date('Y-m-d H:i:s');
				$sms_invite_history->sms = addslashes($sms_body);
				$sms_invite_history->phone = $phone;
				$sms_invite_history->name = $event->organizer_name;
				$sms_invite_history->type = $type;
				$sms_invite_history->save();
			}
		}
	}

	public function sendNewAccountSMS($formInput, $record, $password, $type)
	{
		$template = $sms_organizer_name = "";

		$organizer_id = organizer_id();

		$event = \App\Models\Event::where('id', $formInput['event_id'])->with('info')->first();

		$sms_template = $this->getInviteTemplate($formInput);

		if (isset($sms_template['sms_template']['info'])) {
			foreach ($sms_template['sms_template']['info'] as $row) {
				if ($row['name'] == 'template') {
					$template = $row['value'];
				}
			}
		}

		foreach ($event->info as $val) {
			if ($val->name == 'sms_organizer_name') {
				$sms_organizer_name = $val['value'];
			}
		}

		if (!empty($sms_template)) {
			$sms_body = str_replace("{event_name}", stripslashes($event->name), $template);
			$sms_body = str_replace("{first_name}", stripslashes($record['first_name']), $sms_body);
			$sms_body = str_replace("{last_name}", stripslashes($record['last_name']), $sms_body);
			$sms_body = str_replace("{attendee_name}", stripslashes($record['first_name'] . ' ' . $record['last_name']), $sms_body);
			$sms_body = str_replace("{attendee_email}", stripslashes($record['email']), $sms_body);
			$sms_body = str_replace("{login_link}", cdn('/event/' . $event->url . '/detail'), $sms_body);
			$sms_body = str_replace("{event_organizer_name}", $event->organizer_name, $sms_body);
			$sms_body = str_replace("{app_link}", cdn('/event/' . $event->url), $sms_body);

			if (isset($password) && $password != '') {
				$sms_body = str_replace("{attendee_password}", stripslashes($password), $sms_body);
			} else {
				$sms_body = str_replace("{attendee_password}", '******', $sms_body);
			}

			$sms_body = strip_tags($sms_body);

			if (isset($record['phone']) && $record['phone']) {
				$phone = str_replace(' ', '', (ltrim($record['phone'], '+0')));
				$phone = str_replace('-', '', $phone);
				//send SMS

				$sms_status = sendSMS($sms_body, $phone, $sms_organizer_name);

				//Maintain SMS History
				$sms_invite_history = new \App\Models\EventSmsHistory();
				$sms_invite_history->organizer_id = $organizer_id;
				$sms_invite_history->event_id = $event->id;
				$sms_invite_history->attendee_id = $record['id'];
				$sms_invite_history->email = $record['email'];
				$sms_invite_history->status = $sms_status['status'];
				$sms_invite_history->status_msg = addslashes($sms_status['status_msg']);
				$sms_invite_history->date_sent = date('Y-m-d H:i:s');
				$sms_invite_history->sms = addslashes($sms_body);
				$sms_invite_history->phone = $phone;
				$sms_invite_history->name = $event->organizer_name;
				$sms_invite_history->type = $type;
				$sms_invite_history->save();
			}
		}
	}

	/**
	 *Attendee app invitations
	 *
	 * @param array
	 */

	public function app_invitations($formInput, $count = false)
	{
		//update status for app invitations
		\App\Models\EventAttendee::where('app_invite_sent', 2)->where('event_id', $formInput['event_id'])->update([
			"app_invite_sent" => 1
		]);

		//Event Attendees
		$result = \App\Models\Event::find($formInput['event_id'])->attendees();

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_attendees.id')
				->where('conf_attendees_info.languages_id', '=', $formInput['language_id'])
				->where(function ($query) use ($formInput) {
					$query->where(function ($query) use ($formInput) {
						$query->where('conf_attendees_info.name', '=', 'network_group')
							->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
					})
						->orWhere(function ($query) use ($formInput) {
							$query->where('conf_attendees_info.name', '=', 'delegate_number')
								->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
						})
						->orWhere(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . trim($formInput['query']) . '%')
						->orWhere('email', 'LIKE', '%' . trim($formInput['query']) . '%');
				});
		}

		$result->join('conf_event_attendees AS event_attendee', function ($join) use ($formInput) {
			$join->on('conf_attendees.id', '=', 'event_attendee.attendee_id')
				->where('event_attendee.event_id', $formInput['event_id'])
				->where('event_attendee.app_invite_sent', '=', '1')
				->whereNull('event_attendee.deleted_at');
		});

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('conf_event_attendees.created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('conf_event_attendees.created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		//Attendee Order
		if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('conf_attendees.first_name', 'ASC');
			$result->orderBy('conf_attendees.last_name', 'ASC');
		}

		//Attendee Info
		$result->with(['info' => function ($query) use ($formInput) {
			return $query->where(function ($query) {
				return $query->where('name', '=', 'title')
					->orWhere('name', '=', 'company_name')
					->orWhere('name', '=', 'network_group')
					->orWhere('name', '=', 'phone')
					->orWhere('name', '=', 'website')
					->orWhere('name', '=', 'allow_vote')
					->orWhere('name', '=', 'initial')
					->orWhere('name', '=', 'delegate_number')
					->orWhere('name', '=', 'department')
					->orWhere('name', '=', 'allow_gallery');
			})->where('languages_id', '=', $formInput['language_id']);
		}]);
		$result->groupby('conf_attendees.id');

		if ($count == true) {
			return $result->get()->count();
		} else {
			$attendees = $result->paginate($formInput['limit'])->toArray();

			//Attendee Detail/Groups
			foreach ($attendees['data'] as $key => $row) {
				$info = readArrayKey($row, [], 'info');
				$row = array_merge($info, $row);
				$attendees['data'][$key] = $row;
			}

			return $attendees;
		}
	}

	/**
	 *Attendee app invitations not sent
	 *
	 * @param array
	 */

	public function app_invitations_not_sent($formInput, $count = false)
	{
		//Event Attendees
		$result = \App\Models\Event::find($formInput['event_id'])->attendees();

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_attendees.id')
				->where('conf_attendees_info.languages_id', '=', $formInput['language_id'])
				->where(function ($query) use ($formInput) {
					$query->where(function ($query) use ($formInput) {
						$query->where('conf_attendees_info.name', '=', 'network_group')
							->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
					})
						->orWhere(function ($query) use ($formInput) {
							$query->where('conf_attendees_info.name', '=', 'delegate_number')
								->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
						})
						->orWhere(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . trim($formInput['query']) . '%')
						->orWhere('email', 'LIKE', '%' . trim($formInput['query']) . '%');
				});
		}

		$result->join('conf_event_attendees AS event_attendee', function ($join) use ($formInput) {
			$join->on('conf_attendees.id', '=', 'event_attendee.attendee_id')
				->where('event_attendee.event_id', $formInput['event_id'])
				->where('event_attendee.app_invite_sent', '=', '0')
				->whereNull('event_attendee.deleted_at');
		});

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('conf_event_attendees.created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('conf_event_attendees.created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		//Attendee Order
		if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('conf_attendees.first_name', 'ASC');
			$result->orderBy('conf_attendees.last_name', 'ASC');
		}

		//Attendee Info
		$result->with(['info' => function ($query) use ($formInput) {
			return $query->where(function ($query) {
				return $query->where('name', '=', 'title')
					->orWhere('name', '=', 'company_name')
					->orWhere('name', '=', 'network_group')
					->orWhere('name', '=', 'phone')
					->orWhere('name', '=', 'website')
					->orWhere('name', '=', 'allow_vote')
					->orWhere('name', '=', 'initial')
					->orWhere('name', '=', 'delegate_number')
					->orWhere('name', '=', 'department')
					->orWhere('name', '=', 'allow_gallery');
			})->where('languages_id', '=', $formInput['language_id']);
		}]);

		$result->groupby('conf_attendees.id');

		if ($count == true) {
			return $result->get()->count();
		} else {
			$attendees = $result->paginate($formInput['limit'])->toArray();

			//Attendee Detail/Groups
			foreach ($attendees['data'] as $key => $row) {
				$info = readArrayKey($row, [], 'info');
				$row = array_merge($info, $row);
				$attendees['data'][$key] = $row;
			}

			return $attendees;
		}
	}

	/**
	 *Attendee not registered
	 *
	 * @param array
	 */

	static public function not_registered($formInput, $count = false)
	{
		$registered_attendees = Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select(array('conf_attendees.email'))
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->pluck('email')->toArray();

		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where(function ($query) {
			return $query->where('status', '=', 1)
				->orWhere('sms_sent', '=', 1);
		})
			->where('is_attending', '=', '0')
			->whereNotIn('email', $registered_attendees)
			->where('event_id', $formInput['event_id']);

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->where(function ($query) use ($formInput) {
				return $query->where(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('email', 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('phone', 'LIKE', '%' . $formInput['query'] . '%');
			});
		}

		//Attendee Order
		if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('first_name', 'ASC');
		}

		if ($count == true) {
			return $result->get()->count();
		} else {
			return $result->paginate($formInput['limit'])->toArray();
		}
	}

	/**
	 *move attendees not registered to not attending
	 *
	 * @param array
	 * @param array
	 */

	public function move_not_registered_to_not_attending($formInput, $ids)
	{
		\App\Models\AttendeeInvite::whereIn('id', $ids)->update([
			"is_attending" => 1
		]);
	}

	/**
	 *Not attendees list
	 *
	 * @param array
	 */

	static public function not_attendees_list($formInput, $count = false)
	{
		$registered_attendees = Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select(array('conf_attendees.email'))
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->pluck('email')->toArray();

		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where(function ($query) {
			return $query->where('status', '=', 1)
				->orWhere('sms_sent', '=', 1);
		})
			->where('is_attending', '=', '1')
			->whereNotIn('email', $registered_attendees)
			->where('event_id', $formInput['event_id']);

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->where(function ($query) use ($formInput) {
				return $query->where(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('email', 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('phone', 'LIKE', '%' . $formInput['query'] . '%');
			});
		}

		//Attendee Order
		if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('first_name', 'ASC');
		}

		if ($count == true) {
			return $result->count();
		} else {
			return $result->paginate($formInput['limit'])->toArray();
		}
	}

	/**
	 *Registrations invitation reminder log
	 *
	 * @param array
	 */

	public function registration_invitations_reminder_log($formInput)
	{
		//Event Attendee Invites
		$result = \App\Models\AttendeeInviteLog::where('event_id', $formInput['event_id']);

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->where(function ($query) use ($formInput) {
				return $query->where(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('email', 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('phone', 'LIKE', '%' . $formInput['query'] . '%');
			});
		}

		//Attendee Order
		if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('date_sent', 'DESC');
		}

		$attendees = $result->paginate($formInput['limit'])->toArray();

		return $attendees;
	}

	/**
	 *App invitation reminder log
	 *
	 * @param array
	 */

	public function app_invitations_reminder_log($formInput)
	{
		$query = Attendee::join('conf_event_attendees_app_invite_log', function ($join) use ($formInput) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees_app_invite_log.attendee_id')
				->where('conf_event_attendees_app_invite_log.event_id', $formInput['event_id'])
				->whereNull('conf_event_attendees_app_invite_log.deleted_at');
		})
			->select(array('conf_attendees.*', 'conf_event_attendees_app_invite_log.email_sent', 'conf_event_attendees_app_invite_log.sms_sent', 'conf_event_attendees_app_invite_log.email_date'))
			->where('conf_attendees.organizer_id', organizer_id())
			->where('conf_attendees.deleted_at', '=', NULL)
			->where(function ($query) use ($formInput) {
				if ($formInput['query']) {
					$query->where(\DB::raw('CONCAT(conf_attendees.first_name, " ", conf_attendees.last_name)'), 'LIKE', '%' . trim($formInput['query']) . '%')
						->orWhere('conf_attendees.email', 'LIKE', '%' . trim($formInput['query']) . '%')
						->orWhere('conf_attendees.phone', 'LIKE', '%' . trim($formInput['query']) . '%');
				}
			})
			->orderBy('conf_attendees.first_name', 'ASC');

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$query->whereDate('conf_event_attendees_app_invite_log.created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$query->whereDate('conf_event_attendees_app_invite_log.created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		$records = $query->paginate($formInput['limit']);

		return $records;
	}

	/**
	 *Attendee setting
	 *
	 * @param int
	 */
	static public function getAttendeeSetting($event_id)
	{
		return \App\Models\AttendeeSetting::where('event_id', '=', $event_id)->first();
	}

	/**
	 *update attendee setting
	 *
	 * @param array
	 */
	public function updateAttendeeSetting($formInput)
	{
		return \App\Models\AttendeeSetting::where('event_id', '=', $formInput['event_id'])->update($formInput);
	}

	/**
	 *Export registration invites
	 *
	 * @param array
	 */

	public function getExportRegistrationInvites($formInput)
	{
		$result = \App\Models\AttendeeInvite::where('event_id', $formInput['event_id'])
			->where('status', '=', '0')
			->where('sms_sent', '=', '0')
			->where('not_send', '=', '0')
			->get();

		$attendees = array();
		foreach ($result as $key => $row) {
			$attendees[$key] = array('first_name' => $row->first_name, 'last_name' => $row->last_name, 'phone' => $row->phone, 'email' => $row->email, 'ss_number' => !empty($row->ss_number) ? 'Yes' : 'No', 'allow_vote' => $row->allow_vote, 'ask_to_speak' => $row->ask_to_speak);
		}
		return $attendees;
	}

	/**
	 *Export attendee not registered
	 *
	 * @param array
	 */

	public function getNotRegisteredAttendeesExport($formInput)
	{
		$registered_attendees = Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select(array('conf_attendees.email'))
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->pluck('email')->toArray();

		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where(function ($query) {
			return $query->where('status', '=', 1)
				->orWhere('sms_sent', '=', 1);
		})
			->where('is_attending', '=', '0')
			->whereNotIn('email', $registered_attendees)
			->where('event_id', $formInput['event_id']);

		$attendees = $result->select('first_name', 'last_name', 'phone', 'email')->get()->toArray();

		return $attendees;
	}

	/**
	 *Export not attending list
	 *
	 * @param array
	 */

	public function getNotAttendingListExport($formInput)
	{
		$registered_attendees = Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select(array('conf_attendees.email'))
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->pluck('email')->toArray();

		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where(function ($query) {
			return $query->where('status', '=', 1)
				->orWhere('sms_sent', '=', 1);
		})
			->where('is_attending', '=', '1')
			->whereNotIn('email', $registered_attendees)
			->where('event_id', $formInput['event_id']);


		$attendees = $result->select('first_name', 'last_name', 'phone', 'email')->get()->toArray();

		return $attendees;
	}

	/**
	 *Attendee registration invitations
	 *
	 * @param array
	 */

	static public function registration_invitations($formInput, $count = false)
	{
		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where(function ($query) {
			return $query->where('status', '=', 1)
				->orWhere('sms_sent', '=', 1);
		})
			->where('is_attending', '=', '0')
			->where('event_id', $formInput['event_id']);

		if ($count == true) {
			return $result->count();
		} else {
			return $result->paginate($formInput['limit'])->toArray();
		}
	}

	/**
	 *Invited attendees
	 *
	 * @param array
	 */

	static public function invited_attendees($formInput, $count = false)
	{
		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where(function ($query) {
			return $query->where('status', '=', 1)
				->orWhere('sms_sent', '=', 1);
		})
			->where('event_id', $formInput['event_id']);

		if ($count == true) {
			return $result->count();
		} else {
			return $result->paginate($formInput['limit'])->toArray();
		}
	}

	/**
	 *event attendee registrations date wise count
	 * @param array
	 */
	static public function signup_stats($formInput)
	{
		$query = \App\Models\Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select([
				\DB::raw('DATE(conf_event_attendees.created_at) AS date'),
				\DB::raw('COUNT(conf_event_attendees.id) AS count'),
			])
			->groupBy(\DB::raw('DATE(conf_event_attendees.created_at)'))
			->orderBy('conf_event_attendees.created_at', 'ASC')
			->where('conf_event_attendees.event_id', $formInput['event_id']);

		$event_assigned_attendees = clone $query;
		$attendees = AttendeeRepository::order_attendees($formInput);
		$event_assigned_attendees = $event_assigned_attendees->whereNull('conf_event_attendees.deleted_at')->whereNotIn('conf_attendees.id', $attendees)->get()->toArray();

		$order_attendees = clone $query;
		$attendees = AttendeeRepository::order_attendees($formInput, true, true);
		$order_attendees = $order_attendees->withTrashed()->whereIn('conf_attendees.id', $attendees)->get()->toArray();

		$response = array();

		foreach ($event_assigned_attendees as $key => $row) {
			$response[\Carbon\Carbon::parse($row['date'])->format('Ymd')]['count_1'] = $row['count'];
			$response[\Carbon\Carbon::parse($row['date'])->format('Ymd')]['date'] = \Carbon\Carbon::parse($row['date'])->format('d-m-Y');
		}

		foreach ($order_attendees as $key => $row) {
			$response[\Carbon\Carbon::parse($row['date'])->format('Ymd')]['count_2'] = $row['count'];
			$response[\Carbon\Carbon::parse($row['date'])->format('Ymd')]['date'] = \Carbon\Carbon::parse($row['date'])->format('d-m-Y');
		}

		return $response;
	}


	static public function order_attendees($formInput, $deleted = false, $archive = false)
	{
		//Order attendees
		$query = \App\Models\BillingOrder::where('event_id', $formInput['event_id']);

		if (!$archive) {
			$query->where('is_archive', '=', '0')->where('status', '<>', 'cancelled');
		}

		if ($deleted) {
			$query->withTrashed();
			$orders = $query->with('order_attendees')->get();
		} else {
			$orders = $query->currentOrder()->with('order_attendees')->get();
		}

		$attendees = array();

		foreach ($orders as $order) {
			foreach ($order['order_attendees'] as $attendee) {
				array_push($attendees, $attendee->attendee_id);
			}
		}

		return array_unique($attendees);
	}

	/**
	 *attendee invitations stat
	 * @param array
	 */
	static public function attendee_invitation_stats($formInput)
	{
		return \App\Models\AttendeeInviteStats::where('event_id', $formInput['event_id'])->where('template_alias', $formInput['template_alias'])->select(\DB::raw('sum(send) AS sends'), \DB::raw('sum(case when click > 0 then 1 else 0 end ) AS clicks'), \DB::raw('sum(case when open > 0 then 1 else 0 end ) AS opens'), \DB::raw('sum(hard_bounce) AS hard_bounce'), \DB::raw('sum(soft_bounce) AS soft_bounce'))->first();
	}

	/**
	 *registered attendees
	 * @param array
	 */
	static public function registered_attendees($event_id, $count = true)
	{
		$query = \App\Models\Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select(array('conf_attendees.email'))
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $event_id);
		if ($count == true) {
			return $query->count();
		} else {
			return $query->get();
		}
	}

	/**
	 *Regustered invited attendees
	 *
	 * @param array
	 * @param boolean
	 */

	static public function registered_invited_attendees($formInput, $count = false)
	{
		$registered_attendees = Attendee::join('conf_event_attendees', function ($join) {
			$join->on('conf_attendees.id', '=', 'conf_event_attendees.attendee_id');
		})
			->select(array('conf_attendees.email'))
			->whereNull('conf_event_attendees.deleted_at')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->pluck('email')->toArray();

		//Event Attendee Invites
		$result = \App\Models\AttendeeInvite::where(function ($query) {
			return $query->where('status', '=', 1)
				->orWhere('sms_sent', '=', 1);
		})
			->where('is_attending', '=', '0')
			->whereIn('email', $registered_attendees)
			->where('event_id', $formInput['event_id']);

		//Filter date range
		if (isset($formInput['fromDate']) && $formInput['fromDate']) {
			$result->whereDate('created_at', '>=', \Carbon\Carbon::parse($formInput['fromDate'])->format('Y-m-d'));
		}
		if (isset($formInput['toDate']) && $formInput['toDate']) {
			$result->whereDate('created_at', '<=', \Carbon\Carbon::parse($formInput['toDate'])->format('Y-m-d'));
		}

		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$result->where(function ($query) use ($formInput) {
				return $query->where(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('email', 'LIKE', '%' . $formInput['query'] . '%')
					->orWhere('phone', 'LIKE', '%' . $formInput['query'] . '%');
			});
		}

		//Attendee Order
		if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
			$result->orderBy($formInput['sort_by'], $formInput['order_by']);
		} else {
			$result->orderBy('first_name', 'ASC');
		}

		if ($count == true) {
			return $result->get()->count();
		} else {
			return $result->paginate($formInput['limit'])->toArray();
		}
	}

	/**
	 *send test email
	 *
	 * @param array
	 */
	public function send_test_email($formInput)
	{
		$orgainzer = organizer_info();
		$event = \App\Models\Event::where('id', $formInput['event_id'])->first();
		$templateData = $this->getInviteTemplate($formInput);
		$subject_template = $template = $alias = "";

		if (isset($templateData['email_template']['info'])) {
			$alias = $templateData['email_template']->alias;
			foreach ($templateData['email_template']['info'] as $row) {
				if ($row['name'] == 'template') {
					$template = $row['value'];
				}
				if ($row['name'] == 'subject') {
					$subject_template = $row['value'];
				}
			}
		}

		$contents = stripslashes($template);

		$event_setting  = get_event_branding($formInput['event_id']);
		if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
			$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
		} else {
			$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
		}
		$logo = '<img src="' . $src . '" width="150" />';

		$subject = str_replace("{event_name}", stripslashes($event->name), $subject_template);

		$contents = str_replace("{event_logo}", stripslashes($logo), $contents);
		$contents = str_replace("{event_name}", stripslashes($event->name), $contents);
		$contents = str_replace("{event_organizer_name}", "" . $event->organizer_name, $contents);

		$contents = getEmailTemplate($contents, $formInput['event_id']);

		//email
		$data = array();
		$data['template'] = $alias;
		$data['event_id'] = $formInput['event_id'];
		$data['subject'] = $subject;
		$data['content'] = $contents;
		$data['view'] = 'email.plain-text';
		$data['from_name'] =  $event->organizer_name;
		\Mail::to($formInput['email'])->send(new Email($data));
	}

	/**
	 *Attendee types
	 * @param array
	 */
	static public function attendee_types($formInput)
	{
		$result = \App\Models\EventAttendeeType::where('event_id', $formInput['event_id'])->where('languages_id', $formInput['language_id'])
			->orderBy('sort_order')
			->orderBy('attendee_type')
			->get();

		return $result;
	}

	/**
	 *Update attendee types
	 * @param array
	 */
	public function update_attendee_types($formInput)
	{
		if (isset($formInput['items'])) {
			foreach ($formInput['items'] as $key => $item) {
				\App\Models\EventAttendeeType::where('event_id', $formInput['event_id'])->where('languages_id', $formInput['language_id'])->where('id', $item['id'])->update([
					"status" => $item['status'],
					"attendee_type" => $item['attendee_type'],
					"sort_order" => $key
				]);
			}
		}

		if (isset($formInput['status'])) {
			$billingField = \App\Models\BillingField::where('event_id', $formInput['event_id'])->where('type', 'field')->where('field_alias', "attendee_type")->where('section_alias', "attendee_type_head")->first();
			if ($billingField) {
				$billingField->status = $formInput['status'];
				$billingField->save();
			}
		}
	}

	/**
	 *Add attendee type
	 * @param array
	 */
	public function store_attendee_type($formInput)
	{
		$max = \App\Models\EventAttendeeType::where('event_id', '=', $formInput['event_id'])->max('sort_order');

		\App\Models\EventAttendeeType::create([
			"event_id" => $formInput['event_id'],
			"languages_id" => $formInput['language_id'],
			"attendee_type" => $formInput['name'],
			"status" => 1,
			"is_basic" => 0,
			"sort_order" => ($max + 1),
		]);
	}

	/**
	 *Event attendees
	 * @param array
	 */
	static public function getEventAttendees($formInput, $ids = [], $label = false)
	{
		$query = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])
			->with('attendees');
		if (!empty($ids)) $query->whereIn('attendee_id', $ids);
		$results = $query->get()->toArray();
		$array = array();
		if (count($results) > 0) {
			foreach ($results as $key => $row) {
				if ($label) {
					$array[$key]['value'] = $row['attendees']['id'];
					$array[$key]['label'] = stripslashes($row['attendees']['first_name'] . ' ' . $row['attendees']['last_name']);
				} else {
					$array[$key]['id'] = $row['attendees']['id'];
					$array[$key]['name'] = stripslashes($row['attendees']['first_name'] . ' ' . $row['attendees']['last_name']);
				}
			}
		}
		return $array;
	}

	/**
	 *export event assigned attendees
	 * @param array
	 */

	public function exportAssignAttendees($formInput)
	{
		$event_id = $formInput['event_id'];

		$lang_id = $formInput['language_id'];

		$query = \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->with(['attendee.info' => function ($query) use ($formInput) {
			return $query->where('languages_id', $formInput['language_id']);
		}, 'attendee.adminEventGroups' => function ($query) use ($formInput) {
			return $query->where('event_id', $formInput['event_id'])->whereNull('conf_event_attendees_groups.deleted_at');
		}])->groupBy('attendee_id');

		if (isset($formInput['type']) && $formInput['type'] == "registration-sign-ups") {
			$attendees = AttendeeRepository::order_attendees($formInput);
			$query->whereIn("conf_event_attendees.attendee_id", $attendees);
		}

		$result = $query->get()->toArray();

		$i = 0;

		$attendees = array();

		//Sub registration
		$subRegistration = EventSubRegistration::where('event_id', '=', $event_id)->with(['question.info', 'results' => function ($query) {
			return $query->orderBy('id', 'DESC');
		}])->get()->toArray();

		foreach ($result as $index => $row) {

			if ($row['attendee']) {

				$attendee_type = '';

				if ($row['attendee_type'] > 0) {
					$attendee_type_result = EventAttendeeType::where('id', '=', $row['attendee_type'])->get()->toArray();
					$attendee_type = $attendee_type_result[0]['attendee_type'];
				}
				$temp = array();

				if (count($row['attendee']['info']  ?? []) > 0) {
					foreach ($row['attendee']['info'] as $val) {
						if ($val['name'] == 'custom_field_id' . $event_id) {
							$val['name'] = 'custom_field_id';
							$values = explode(',', $val['value']);
							$field_data = '';
							if (trim($val['value']) != '' && $val['value'] != 0) {
								foreach ($values as $field) {
									$field_info = EventCustomFieldInfo::where('custom_field_id', '=', $field)->where('languages_id', '=', $lang_id)->get()->toArray();
									$field_info = $field_info[0];
									$field_data .= $field_info['value'] . ',';
								}
							}
							$temp[$val['name']] = substr($field_data, 0, -1);
						} else {
							$temp[$val['name']] = $val['value'];
						}
					}
				}

				//Info data is here--------------------------------------------------------

				$phone = explode("-", $temp['phone']);
				$code = ltrim($phone[0], '+');
				$phone_number = $phone[1];

				$row['id'] = $row['attendee']['id'];
				$row['email'] = $row['attendee']['email'];
				$row['first_name'] = $row['attendee']['first_name'];
				$row['last_name'] = $row['attendee']['last_name'];
				$row['organizer_id'] = $row['attendee']['organizer_id'];
				$row['initial'] = $temp['initial'];
				$row['title'] = $temp['title'];
				$row['company_name'] = $temp['company_name'];
				$row['about'] = preg_replace("/\r|\n/", "<br>", $temp['about']);
				$row['industry'] = $temp['industry'];
				$row['website'] = $temp['website'];
				$row['facebook'] = $temp['facebook'];
				$row['twitter'] = $temp['twitter'];
				$row['linkedin'] = $temp['linkedin'];
				$row['country'] = $temp['country'];
				$row['organization'] = $temp['organization'];
				$row['jobs'] = $temp['jobs'];
				$row['interests'] = $temp['interests'];
				$row['age'] = $temp['age'];
				$row['gender'] = $temp['gender'];
				$row['calling_code'] = $code;
				$row['phone'] = $phone_number;
				$row['allow_vote'] = $row['allow_vote'];
				$row['department'] = $temp['department'];
				$row['allow_gallery'] = $row['allow_gallery'];
				$row['ask_to_apeak'] = $row['ask_to_apeak'];
				$row['network_group'] = $temp['network_group'];
				$row['delegate_number'] = $temp['delegate_number'];
				$row['table_number'] = $temp['table_number'];
				$row['custom_field_id'] = $temp['custom_field_id'];
				$row['FIRST_NAME_PASSPORT'] = $row['attendee']['FIRST_NAME_PASSPORT'];
				$row['LAST_NAME_PASSPORT'] = $row['attendee']['LAST_NAME_PASSPORT'];
				$row['BIRTHDAY_YEAR'] = date('Y-m-d', strtotime($row['attendee']['BIRTHDAY_YEAR']));
				$row['place_of_birth'] = $temp['place_of_birth'];
				$row['passport_no'] = $temp['passport_no'];
				$row['date_of_issue_passport'] = $temp['date_of_issue_passport'];
				$row['date_of_expiry_passport'] = $temp['date_of_expiry_passport'];
				$row['EMPLOYMENT_DATE'] = $row['attendee']['EMPLOYMENT_DATE'];
				$row['SPOKEN_LANGUAGE'] = $row['attendee']['SPOKEN_LANGUAGE'];
				$row['type_resource'] = $row['type_resource'];
				$row['allow_my_document'] = $row['allow_my_document'];
				$row['private_house_number'] = $temp['private_house_number'];
				$row['private_street'] = $temp['private_street'];
				$row['private_post_code'] = $temp['private_post_code'];
				$row['private_city'] = $temp['private_city'];
				$row['private_country'] = $temp['private_country'];

				if ($row['attendee']['ss_number'] != '') {
					$ss_number = 'Yes';
				} else {
					$ss_number = 'No';
				}

				$row['ss_number'] = $ss_number;

				if ($row['gdpr'] == '1') {
					$gdpr = 'Yes';
				} else {
					$gdpr = 'No';
				}

				//Group ID--------------------------------------------------------
				$event_groups = array();

				if (count($row['attendee']['admin_event_groups']  ?? []) > 0) {

					$k = 0;
					foreach ($row['attendee']['admin_event_groups'] as $group_detail) {

						$event_groups[] = $group_detail['id'];
						$k++;
					}
				}

				// Country Code
				if ($row['country'] != '') {
					$country_code = Country::where('id', '=', $row['country'])->get()->toArray();
					$country_id = $country_code[0]['code_2'];
				} else {
					$country_id = '';
				}


				unset($row['attendee']['admin_event_groups']);

				$row['group_id'] = implode(';', $event_groups);

				unset($row['status'], $row['email_sent'], $row['sms_sent'], $row['login_yet'], $row['event_id'], $row['attendee'], $row['attendee']['info'], $row['attendee']['info'], $row['languages_id'], $row['created_at'], $row['updated_at'], $row['deleted_at'], $row['attendee_id'], $row['password'], $row['image'], $row['show_home'], $row['default_language_id']);

				$characters = array('¿' => 'ø', 'Œ' => 'å', '¾' => 'æ', '¯' => 'Ø', 'Ž' => 'é', '®' => 'Æ', 'â' => 'ä', 'Ÿ' => 'ü', '' => 'å', '' => 'é');
				$first_name = $row['first_name'];
				$last_name = $row['last_name'];
				$title = $row['title'];
				$company_name = $row['company_name'];
				$about = $row['about'];
				$network_group = $row['network_group'];
				$industry = $row['industry'];

				foreach ($characters as $key => $char) {
					$first_name = str_replace($key, $char, $first_name);
					$last_name = str_replace($key, $char, $last_name);
					$title = str_replace($key, $char, $title);
					$company_name = str_replace($key, $char, $company_name);
					$about = str_replace($key, $char, $about);
					$network_group = str_replace($key, $char, $network_group);
					$industry = str_replace($key, $char, $industry);
				}

				$sub_registration_results = array();

				foreach ($subRegistration as $subReg) {
					foreach ($subReg['question'] as $key => $temp_question) {
						$sub_registration_results[$temp_question['info'][0]['value']] = '';
						$sub_registration_results['comments' . $key] = '';
						if ($temp_question['question_type'] == 'open') {
							foreach ($subReg['results'] as $temp_answer1) {
								if ($temp_answer1['question_id'] == $temp_question['id'] && $temp_answer1['attendee_id'] == $row['id']) {
									$sub_registration_results[$temp_question['info'][0]['value']] = $temp_answer1['answer'];
									if (count((array)$temp_answer1['comments']  ?? []) > 0) {
										$sub_registration_results['comments' . $key] = $temp_answer1['comments'];
									}
									break;
								}
							}
						} else if ($temp_question['question_type'] == 'single') {
							foreach ($subReg['results'] as $temp_answer2) {
								if ($temp_answer2['question_id'] == $temp_question['id'] && $temp_answer2['attendee_id'] == $row['id']) {
									$answer_value = EventSubRegistrationAnswer::where('id', '=', $temp_answer2['answer_id'])->with(['info' => function ($query) use ($lang_id) {
										return $query->where('languages_id', '=', $lang_id);
									}])->get()->toArray();
									if (count($answer_value) > 0) {
										$answer_value = $this->returnInfoArray($answer_value);
										$answer_value = $answer_value[0];
										$sub_registration_results[$temp_question['info'][0]['value']] = $answer_value['answer'];
									} else {
										$row[$temp_question['info'][0]['value']] = '';
									}
									if (count((array)$temp_answer2['comments']  ?? []) > 0) {
										$sub_registration_results['comments' . $key] = $temp_answer2['comments'];
									}
									break;
								}
							}
						} else if ($temp_question['question_type'] == 'date_time') {
							foreach ($subReg['results'] as $temp_answer) {
								if ($temp_answer['question_id'] == $temp_question['id'] && $temp_answer['attendee_id'] == $row['id']) {
									$sub_registration_results[$temp_question['info'][0]['value']] = date('d-m-Y H:i', strtotime($temp_answer['answer']));

									if (count((array)$temp_answer['comments']  ?? []) > 0) {
										$sub_registration_results['comments' . $key] = $temp_answer['comments'];
									}
									break;
								}
							}
						} else if ($temp_question['question_type'] == 'date') {
							foreach ($subReg['results'] as $temp_answer) {
								if ($temp_answer['question_id'] == $temp_question['id'] && $temp_answer['attendee_id'] == $row['id']) {
									$sub_registration_results[$temp_question['info'][0]['value']] = date('d-m-Y', strtotime($temp_answer['answer']));
									if (count((array)$temp_answer['comments']  ?? []) > 0) {
										$sub_registration_results['comments' . $key] = $temp_answer['comments'];
									}
									break;
								}
							}
						} else if ($temp_question['question_type'] == 'number') {
							foreach ($subReg['results'] as $temp_answer) {
								if ($temp_answer['question_id'] == $temp_question['id'] && $temp_answer['attendee_id'] == $row['id']) {
									$sub_registration_results[$temp_question['info'][0]['value']] = $temp_answer['answer'];

									if (count((array)$temp_answer['comments']  ?? []) > 0) {
										$sub_registration_results['comments' . $key] = $temp_answer['comments'];
									}
									break;
								}
							}
						} else if ($temp_question['question_type'] == 'dropdown') {
							foreach ($subReg['results'] as $temp_answer) {
								if ($temp_answer['question_id'] == $temp_question['id'] && $temp_answer['attendee_id'] == $row['id']) {
									$answer_value = EventSubRegistrationAnswer::where('id', '=', $temp_answer['answer_id'])->with(['info' => function ($query) use ($lang_id) {
										return $query->where('languages_id', '=', $lang_id);
									}])->get()->toArray();
									if (count($answer_value  ?? []) > 0) {
										$answer_value = $this->returnInfoArray($answer_value);
										$answer_value = $answer_value[0];
										$sub_registration_results[$temp_question['info'][0]['value']] = $answer_value['answer'];
									} else {
										$sub_registration_results[$temp_question['info'][0]['value']] = '';
									}
									if (count((array)$temp_answer['comments']  ?? []) > 0) {
										$sub_registration_results['comments' . $key] = $temp_answer['comments'];
									}
									break;
								}
							}
						} else {
							$answer_string = '';
							$l = 0;
							foreach ($subReg['results'] as $temp_answer3) {
								if ($temp_answer3['question_id'] == $temp_question['id'] && $temp_answer3['attendee_id'] == $row['id']) {
									$answer_value = EventSubRegistrationAnswer::where('id', '=', $temp_answer3['answer_id'])->with(['info' => function ($query) use ($lang_id) {
										return $query->where('languages_id', '=', $lang_id);
									}])->get()->toArray();
									if (count($answer_value  ?? []) > 0) {
										$answer_value = $this->returnInfoArray($answer_value);
										$answer_value = $answer_value[0];
										$comma = '';
										if ($l > 0) {
											$comma = ',';
										}
										$answer_string .= $comma . $answer_value['answer'];
										$sub_registration_results[$temp_question['info'][0]['value']] = $answer_string;
									}
									if (count((array)$temp_answer3['comments'] ?? []) > 0) {
										$sub_registration_results['comments' . $key] = $temp_answer3['comments'];
									}
									$l++;
								}
							}
						}
					}
				}

				$attendees[$i] = array(
					'attendee_id' => $row['id'], 'initial' => $row['initial'], 'first_name' => $first_name, 'last_name' => $last_name, 'title' => $title, 'company_name' => $company_name, 'about' => html_entity_decode($about), 'industry' => $industry, 'email' => $row['email'], 'website' => $row['website'], 'facebook' => $row['facebook'], 'twitter' => $row['twitter'], 'linkedin' => $row['linkedin'],
					'country' => $country_id, 'organization' => $row['organization'], 'jobs' => $row['jobs'], 'interests' => $row['interests'], 'age' => $row['age'], 'gender' => $row['gender'],
					'calling_code' => $row['calling_code'], 'phone' => $row['phone'], 'allow_vote' => $row['allow_vote'], 'group_id' => $row['group_id'], 'organizer_id' => $row['organizer_id'],
					'department' => $row['department'], 'custom_field_id' => $row['custom_field_id'], 'allow_gallery' => $row['allow_gallery'], 'ask_to_apeak' => $row['ask_to_apeak'],
					'network_group' => $network_group, 'delegate_number' => $row['delegate_number'], 'table_number' => $row['table_number'], 'FIRST_NAME_PASSPORT' => $row['FIRST_NAME_PASSPORT'],
					'LAST_NAME_PASSPORT' => $row['LAST_NAME_PASSPORT'], 'BIRTHDAY_YEAR' => $row['BIRTHDAY_YEAR'], 'place_of_birth' => $row['place_of_birth'], 'passport_no' => $row['passport_no'], 'date_of_issue_passport' => $row['date_of_issue_passport'], 'date_of_expiry_passport' => $row['date_of_expiry_passport'], 'EMPLOYMENT_DATE' => $row['EMPLOYMENT_DATE'], 'SPOKEN_LANGUAGE' => $row['SPOKEN_LANGUAGE'],
					'ss_number' => $row['ss_number'], 'gdpr' => $gdpr, 'attendee_type_id' => $row['attendee_type'], 'attendee_type' => $attendee_type,
					'type_resource' => $row['type_resource'], 'allow_my_document' => $row['allow_my_document'], 'private_house_number' => $row['private_house_number'], 'private_street' => $row['private_street'], 'private_post_code' => $row['private_post_code'], 'private_city' => $row['private_city'], 'private_country' => $row['private_country']
				);

				$attendees[$i] = array_merge($attendees[$i], $sub_registration_results);

				$i++;
			}
		}

		return $attendees;
	}

	static public function getExportSettingWithSubReg($formInput)
	{

		$event_id = $formInput['event_id'];
		$lang_id = $formInput['language_id'];

		$labels = EventSiteText::where('event_id', '=', $event_id)
			->where('parent_id', '=', '0')
			->where('module_alias', '=', 'attendees')
			->with(['info' => function ($query) use ($lang_id) {
				return $query->where('languages_id', '=', $lang_id);
			}])
			->with(['children' => function ($r) {
				return $r->orderBy('constant_order');
			}, 'children.childrenInfo' => function ($rr) use ($lang_id) {
				return $rr->where('languages_id', '=', $lang_id);
			}])
			->orderBy('section_order')->get()->toArray();
		$attendeeLabels = [];
		foreach ($labels[0]['children'] as $row) {
			if (count($row['children_info'] ?? []) > 0) {
				foreach ($row['children_info'] as $val) {
					$attendeeLabels[$row['alias']] = $val['value'];
				}
			}
		}

		$settings = array(
			'fields' => array(
				'initial' => array(
					'field' => 'initial',
					'label' => 'Initial',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'first_name' => array(
					'field' => 'first_name',
					'label' => 'First Name',
					'type' => 'string',
					'required' => true
				),
				'last_name' => array(
					'field' => 'last_name',
					'label' => 'Last Name',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'title' => array(
					'field' => 'title',
					'label' => 'Title',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_TITLE'
				),
				'company_name' => array(
					'field' => 'company_name',
					'label' => 'Company Name',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_COMPANY_NAME'
				),
				'about' => array(
					'field' => 'about',
					'label' => 'About Me',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_ABOUT'
				),
				'industry' => array(
					'field' => 'industry',
					'label' => 'Industry',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_INDUSTRY'
				),
				'email' => array(
					'field' => 'email',
					'label' => 'Email',
					'type' => 'string',
					'required' => true,
					'alias' => ''
				),
				'website' => array(
					'field' => 'website',
					'label' => 'Website',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'facebook' => array(
					'field' => 'facebook',
					'label' => 'Facebook',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'twitter' => array(
					'field' => 'twitter',
					'label' => 'Twitter',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'linkedin' => array(
					'field' => 'linkedin',
					'label' => 'LinkedIn',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'country_iso' => array(
					'field' => 'country',
					'label' => 'Country ISO',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_COUNTRY'
				),
				'organization' => array(
					'field' => 'organization',
					'label' => 'Organization',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_ORGANIZATION'
				),
				'jobs' => array(
					'field' => 'jobs',
					'label' => 'Job Tasks',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_JOB_TASKS'
				),
				'interests' => array(
					'field' => 'interests',
					'label' => 'Interests',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_INTERESTS'
				),
				'age' => array(
					'field' => 'age',
					'label' => 'Age',
					'type' => 'integer',
					'required' => false,
					'alias' => ''
				),
				'gender' => array(
					'field' => 'gender',
					'label' => 'Gender',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'country_code' => array(
					'field' => 'country_code',
					'label' => 'Country code',
					'type' => 'integer',
					'required' => false,
					'alias' => ''
				),
				'phone' => array(
					'field' => 'phone',
					'label' => 'Phone',
					'type' => 'integer',
					'required' => false,
					'alias' => 'ATTENDEE_PHONE'
				),
				'allow_vote' => array(
					'field' => 'allow_vote',
					'label' => 'Voting Permissions',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'group_id' => array(
					'field' => 'group_id',
					'label' => 'Group Id',
					'type' => 'list',
					'required' => false,
					'alias' => 'ATTENDEE_LIST_BY_GROUP'
				),
				'organizer_id' => array(
					'field' => 'organizer_id',
					'label' => 'Organizer Id',
					'type' => 'integer',
					'required' => false,
					'alias' => ''
				),
				'department' => array(
					'field' => 'department',
					'label' => 'Department',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_DEPARTMENT'
				),
				'custom_field_id' => array(
					'field' => 'custom_field_id',
					'label' => 'Custom Field',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'allow_gallery' => array(
					'field' => 'allow_gallery',
					'label' => 'Image Gallery',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'ask_to_apeak' => array(
					'field' => 'ask_to_apeak',
					'label' => 'Ask to speak',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'network_group' => array(
					'field' => 'network_group',
					'label' => 'Network Group',
					'type' => 'string',
					'required' => false,
					'alias' => 'GENERAL_NETWORK_GROUP'
				),
				'delegate_number' => array(
					'field' => 'delegate_number',
					'label' => 'Delegate Number',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_DELEGATE_NUMBER'
				),
				'table_number' => array(
					'field' => 'table_number',
					'label' => 'Table Number',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_TABLE_NUMBER'
				),
				'FIRST_NAME_PASSPORT' => array(
					'field' => 'FIRST_NAME_PASSPORT',
					'label' => 'First Name (Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'LAST_NAME_PASSPORT' => array(
					'field' => 'LAST_NAME_PASSPORT',
					'label' => 'Last Name (Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'BIRTHDAY_YEAR' => array(
					'field' => 'BIRTHDAY_YEAR',
					'label' => 'Date of Birth',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'place_of_birth' => array(
					'field' => 'place_of_birth',
					'label' => 'Place of birth(Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PLACE_OF_BIRTH'
				),
				'passport_no' => array(
					'field' => 'passport_no',
					'label' => 'Passport no',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PASSPORT_NO'
				),
				'date_of_issue_passport' => array(
					'field' => 'date_of_issue_passport',
					'label' => 'Date of issue(Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PASSPORT_ISSUE_DATE'
				),
				'date_of_expiry_passport' => array(
					'field' => 'date_of_expiry_passport',
					'label' => 'Date of expiry(Passport)',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PASSPORT_EXPIRY_DATE'
				),
				'EMPLOYMENT_DATE' => array(
					'field' => 'EMPLOYMENT_DATE',
					'label' => 'Employment Date',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'SPOKEN_LANGUAGE' => array(
					'field' => 'SPOKEN_LANGUAGE',
					'label' => 'Languages',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'ss_number' => array(
					'field' => 'ss_number',
					'label' => 'Social security number',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'gdpr' => array(
					'field' => 'gdpr',
					'label' => 'GDPR',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'attendee_type_id' => array(
					'field' => 'attendee_type_id',
					'label' => 'Attendee type id',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'attendee_type' => array(
					'field' => 'attendee_type',
					'label' => 'Attendee type',
					'type' => 'string',
					'required' => false,
					'alias' => ''
				),
				'type_resource' => array(
					'field' => 'type_resource',
					'label' => 'Type Resource',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'allow_my_document' => array(
					'field' => 'allow_my_document',
					'label' => 'Allow Document',
					'type' => 'booleans',
					'required' => false,
					'alias' => ''
				),
				'private_house_number' => array(
					'field' => 'private_house_number',
					'label' => 'Private house number',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_HOUSE_NUMBER'
				),
				'private_street' => array(
					'field' => 'private_street',
					'label' => 'Private street',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_STREET'
				),
				'private_post_code' => array(
					'field' => 'private_post_code',
					'label' => 'Private post code',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_POST_CODE'
				),
				'private_city' => array(
					'field' => 'private_city',
					'label' => 'Private city',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_CITY'
				),
				'private_country' => array(
					'field' => 'private_country',
					'label' => 'Private country',
					'type' => 'string',
					'required' => false,
					'alias' => 'ATTENDEE_PRIVATE_COUNTRY'
				),
				'attendee_id' => array(
					'field' => 'attendee_id',
					'label' => 'Attendee ID',
					'type' => 'string',
					'required' => true
				)

			),
			'add_url' => '_admin/attendee/create',
			'list_url' => '_admin/attendee',
			'export_url' => '_admin/attendee/export'
		);

		$subRegistration = EventSubRegistration::where('event_id', '=', $event_id)->with(['question.info'])->get()->toArray();

		foreach ($settings['fields'] as $key => $field) {
			if (isset($attendeeLabels[$field['alias']])) {
				$settings['fields'][$key]['label'] = $attendeeLabels[$field['alias']];
			}
		}

		foreach ($subRegistration as $subReg) {
			foreach ($subReg['question'] as $key => $question) {
				$temp_setting1[$key] = array(
					$question['info'][0]['value'] => array(
						'field' => $question['info'][0]['value'],
						'label' => $question['info'][0]['value'],
						'type' => 'string',
						'required' => false
					), 'comments' . $key => array(
						'field' => 'comments ',
						'label' => 'Comments',
						'type' => 'string',
						'required' => false
					),
				);
				$settings['fields'] = array_merge($settings['fields'], $temp_setting1[$key]);
			}
		}

		return $settings;
	}

	public function returnInfoArray($array)
	{
		$final_array = array();
		if (count($array) > 0) {
			foreach ($array as $record) {

				$temp_array = array();
				foreach ($record as $key => $rec) {
					if ($key == 'info') {
						foreach ($record['info'] as $info) {
							$temp_array[$info['name']] = $info['value'];
						}
					} else if ($key == 'settings' && is_array($rec)) {
						foreach ($record['settings'] as $setting) {
							$temp_array[$setting['name']] = $setting['value'];
						}
					} else {
						$temp_array[$key] = $rec;
					}
				}
				$final_array[] = $temp_array;
			}
		} else {
			return $array;
		}
		return $final_array;
	}

	/**
	 * @api Zapier
	 * @param $formInput
	 * @return Collection
	 */
	public function getZapierAttendees($formInput)
	{
		$organizer = auth()->guard('organizer')->user();

		$event_ids = AddAttendeeLog::select('event_id')->where('organizer_id', $organizer->id)->distinct('event_id')->get()->pluck('event_id');
		$attendees = [];
		foreach ($event_ids as $event_id) {

			$event = Event::find($event_id);
			$lang_id = $event->language_id;
			$attendeeLogs = AddAttendeeLog::where('organizer_id', $organizer->id)->where('event_id', $event_id)->with(['attendee.info' => function ($query) use ($lang_id) {
				$query->where('languages_id', $lang_id);
			}])->get();

			foreach ($attendeeLogs as $log) {
				$log->attendee->event_id = $event_id;
				$log->attendee->log_id = $log->id;
				$attendees[] = $log->attendee;
			}
		}

		return (new Collection($attendees))->sortByDesc('created_at');
	}


	/**
	 * @param $organizer_id
	 * @return array
	 * @api Salesforce
	 */
	public static function getAttendeeLog($organizer_id)
	{
		$event_ids = AddAttendeeLog::select('event_id')->where('organizer_id', $organizer_id)->where('status', '!=', 1)->distinct('event_id')->get()->pluck('event_id');
		$attendees = [];
		foreach ($event_ids as $event_id) {

			$event = Event::find($event_id);
			$lang_id = $event->language_id;
			$attendeeLogs = AddAttendeeLog::where('organizer_id', $organizer_id)->where('event_id', $event_id)->where('status', '!=', 1)->with(['event', 'attendee.billing.country', 'attendee.info' => function ($query) use ($lang_id) {
				$query->where('languages_id', $lang_id);
			}])->get()->toArray();

			foreach ($attendeeLogs as $log) {
				$log['attendee']['log_id'] = $log['id'];
				$log['attendee']['event'] = $log['event']['name'];

				foreach ($log['attendee']['info'] as $info) {
					if ($info['name'] == 'private_country') {
						$country = Country::find($info['value']);
						$info['value'] = $country->name;
					}
					$log['attendee'][$info['name']] = $info['value'];
				}
				$attendees[] = $log['attendee'];
			}
		}

		return $attendees;
	}

	/**
	 * @param mixed $formInput
	 * @param mixed $action
	 * 
	 * @return [type]
	 */
	public function update_gdpr($formInput, $action)
	{
		$event = $formInput['event'];
		$attendee_detail = $formInput['attendee_detail'];
		$event_id = $event['id'];
		$organizer_id = $event['organizer_id'];
		$attendee_id = $formInput['attendee_detail']['id'];

		//Event attendee
		$event_attendee = $attendee_detail['event_attendee'];

		//update event attendee
		\App\Models\EventAttendee::where('event_id', $event_id)->where('attendee_id', $attendee_id)->update([
			"gdpr" => ($action == "cancel" ? 0 : 1)
		]);

		//event gdpr info
		$gdpr = \App\Models\EventGdpr::where('event_id', $event_id)->first();

		//gdpr setting
		$gdpr_setting = \App\Models\EventGdprSetting::where('event_id', $event_id)->first();

		if ($gdpr_setting->enable_gdpr == 1 || $action == "accept") {
			$log['event_id'] = $event_id;
			$log['attendee_id'] = $attendee_id;
			$log['gdpr_accept'] = ($action == "cancel" ? 0 : 1);
			$log['gdpr_description'] = $gdpr->description;
			\App\Models\GdprAttendeeLog::create($log);
		}

		//send email
		if ($action == "cancel") {
			$attendee = \App\Models\Attendee::where('email', $attendee_detail['email'])->where('organizer_id', $organizer_id)->first();
			$sms_organizer_name = isset($event['detail']['sms_organizer_name']) ? $event['detail']['sms_organizer_name'] : '';
			$template = getTemplate('email', 'gdpr', $event['id'], $event['language_id']);
			if ($template) {
				$event_setting = get_event_branding($event['id']);
				if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
					$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
				} else {
					$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
				}

				$logo = '<img src="' . $src . '" width="150" />';

				$logo = '<img src="' . $src . '" width="150" />';

				$name = stripslashes($attendee['first_name'] . ' ' . $attendee['last_name']);

				$subject = str_replace("{event_name}", stripslashes($event['name']), $template->info[0]['value']);
				$template = getEmailTemplate($template->info[1]['value'], $event['id']);
				$contents = stripslashes($template);
				$contents = str_replace("{event_logo}", stripslashes($logo), $contents);
				$contents = str_replace("{event_name}", stripslashes($event['name']), $contents);
				$contents = str_replace("{event_organizer_name}", "" . $event['organizer_name'], $contents);
				$contents = str_replace("{attendee_name}", stripslashes($name), $contents);
				$contents = str_replace("{attendee_email}", stripslashes($attendee['email']), $contents);
				$contents = str_replace("{date}", date('Y-m-d'), $contents);
				$contents = str_replace("{time}", date('H:i:s'), $contents);

				//Bcc emails
				$bcc_emails = \App\Models\EventGdprSetting::where('event_id', $event['id'])->value('bcc_emails');
				$emails = ($bcc_emails ? explode(',', $bcc_emails) : []);

				$data = array();
				$data['event_id'] = $event->id;
				$data['subject'] = $subject;
				$data['content'] = $contents;
				$data['view'] = 'email.plain-text';
				$data['from_name'] =  $event->organizer_name;
				if (count($emails) > 0) \Mail::to($emails)->send(new Email($data));
			}
		}

		return true;
	}

	/**
	 * @param mixed $formInput
	 * @param bool $count
	 * 
	 * @return [type]
	 */
	static public function gdprLog($formInput, $count = false)
	{
		$query = \App\Models\GdprAttendeeLog::where('event_id', $formInput['event_id'])->where('attendee_id', $formInput['attendee_id']);
		if ($count) {
			return $query->count();
		} else {
			return $query->get();
		}
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function getEventattendee($formInput)
	{
		return \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->where('attendee_id', $formInput['attendee_id'])->first();
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	function getAttendeeDetail($formInput)
	{
		return \App\Models\Attendee::where('id', $formInput['attendee_id'])->first();
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function getAllParentGroups($formInput)
	{
		$groups = \App\Models\EventGroup::where('event_id', $formInput['event_id'])
			->where('parent_id', '=', '0')
			->with(['info' => function ($query) use ($formInput) {
				return $query->where('languages_id', $formInput['language_id']);
			}])
			->with(['children' => function ($r) {
				return $r->orderBy('sort_order');
			}, 'children.childrenInfo' => function ($query) use ($formInput) {
				return $query->where('languages_id', $formInput['language_id']);
			}, 'children.assignAttendeeGroups'])
			->orderBy('sort_order')->get();

		return $groups;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function getAttendeeGroupsIds($formInput)
	{
		$attendeeGroups = \App\Models\EventAttendeeGroup::where('attendee_id', $formInput['attendee_id'])->get();
		$groups = self::getAllParentGroups($formInput);
		$groups_array = array();
		foreach ($groups as $group) {
			if (count($group['children']) > 0) {
				foreach ($group['children'] as $mod) {
					foreach ($attendeeGroups as $attendeeGroup) {
						if ($mod['id'] == $attendeeGroup['group_id']) {
							array_push($groups_array, $attendeeGroup['group_id']);
						}
					}
				}
			}
		}

		return $groups_array;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function getAttendeeGroups($formInput)
	{
		$attendeeGroups = \App\Models\EventAttendeeGroup::where('attendee_id', $formInput['attendee_id'])->get();
		$groups = self::getAllParentGroups($formInput);
		$groups_array = array();
		if ($groups) {
			foreach ($groups->toArray() as $group) {
				if (count($group['children']) > 0) {
					$groups_array[$group['id']]['id'] = $group['info']['group_id'];
					$groups_array[$group['id']]['name'] = $group['info']['value'];
					$groups_array[$group['id']]['allow_multiple'] = $group['allow_multiple'];
					foreach ($group['children'] as $child) {
						$present = false;
						foreach ($attendeeGroups as $attendeeGroup) {
							if ($child['id'] == $attendeeGroup['group_id']) {
								$present = true;
							}
						}

						$groups_array[$group['id']]['child'][$child['id']] = array('present' => $present, 'info' => $child['children_info'][0]);
					}
				}
			}
		}

		return $groups_array;
	}

	/**
	 * @param $formInput
	 * @return mixed
	 */
	public function getEventSpeakers($formInput)
	{
		$lang_id = $formInput['language_id'];

		$speakers = Attendee::join('conf_event_agenda_speakers', 'conf_event_agenda_speakers.attendee_id', '=', 'conf_attendees.id')
			->select('conf_attendees.*')
			->where('conf_event_agenda_speakers.event_id', $formInput['event_id'])
			->whereNull('conf_attendees.deleted_at')
			->whereNull('conf_event_agenda_speakers.deleted_at')
			->groupBy('conf_attendees.id')
			->with(['info' => function ($q) use ($lang_id) {
				$q->where('languages_id', $lang_id);
			}, 'currentEventAttendee']);

		if (isset($formInput['home']) && $formInput['home']) {
			$speakers->where('conf_event_agenda_speakers.eventsite_show_home', 1);
		}

		if (isset($formInput['category_id']) && $formInput['category_id']) {
			$speaker_ids = \App\Models\EventSpeakerCategory::select('speaker_id')->where('category_id', $formInput['category_id'])->get()->pluck('speaker_id');
			$speakers->whereIn('conf_attendees.id', $speaker_ids);
		}

		if ($formInput['event']['gdpr_settings']['enable_gdpr'] && $formInput['event']['gdpr_settings']['attendee_invisible']) {
			$speakers->whereHas('currentEventAttendee', function ($query) {
				$query->where('gdpr', '=', '1');
			});
		}

		if (isset($formInput['query']) && $formInput['query']) {
			$speakers->where(function ($query) use ($formInput) {
				$query->where(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . trim($formInput['query']) . '%')
					->orWhere('email', 'LIKE', '%' . trim($formInput['query']) . '%');
			});
		}

		$orderby = $formInput['event']['speaker_settings']['order_by'] === "custom" ? "conf_event_agenda_speakers.sort_order"  : "conf_attendees." . $formInput['event']['speaker_settings']['order_by'];
		
		$limit = isset($formInput['home']) && $formInput['home'] ? $formInput['event']['speaker_settings']['registration_site_limit'] : $formInput['limit'];
		
		$speakers = $speakers->orderBy($orderby)->paginate($limit);

		// display Setting refactor
		foreach ($speakers as $key => $value) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'speaker', $lang_id);
			$value = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $value, $attendee_type_id);
			$value->gdpr = $formInput['event']['gdpr_settings']['enable_gdpr'] ? $value->currentEventAttendee->gdpr : 1;
			$speakers[$key] = $value;
		}

		return $speakers;
	}

	/**
	 * @param $formInput
	 * @return mixed
	 */
	public function getFrontEventAttendees($formInput)
	{
		$lang_id = $formInput['language_id'];

		$speaker_settings = $formInput["event"]["speaker_settings"];

		$sponsor_settings = $formInput["event"]["sponsor_settings"];

		$exhibitor_settings = $formInput["event"]["exhibitor_settings"];

		$attendee = Attendee::join('conf_event_attendees', 'conf_event_attendees.attendee_id', '=', 'conf_attendees.id')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->select('conf_attendees.*')
			->whereNull('conf_attendees.deleted_at')
			->whereNull('conf_event_attendees.deleted_at')
			->with(['info' => function ($q) use ($lang_id) {
				$q->where('languages_id', $lang_id);
			}, 'currentEventAttendee']);

		if ($formInput['event']['gdpr_settings']['enable_gdpr'] && $formInput['event']['gdpr_settings']['attendee_invisible']) {
			$attendee->whereHas('currentEventAttendee', function ($query) use ($formInput) {
				$query->where('gdpr', '=', '1');
			});
		}

		if ($speaker_settings['hide_attendee']  || $sponsor_settings['hide_attendee'] || $exhibitor_settings['hide_attendee']) {
			$attendee->whereHas('currentEventAttendee', function ($query) use ($speaker_settings, $sponsor_settings, $exhibitor_settings) {
				if ($speaker_settings['hide_attendee']) {
					$query->where('speaker', '=', '0');
				}
				if ($sponsor_settings['hide_attendee']) {
					$query->where('sponser', '=', '0');
				}
				if ($exhibitor_settings['hide_attendee']) {
					$query->where('exhibitor', '=', '0');
				}
			});
		}
		//search
		if (isset($formInput['query']) && $formInput['query']) {
			$attendee->join('conf_attendees_info', 'conf_attendees_info.attendee_id', '=', 'conf_attendees.id')
				->where('conf_attendees_info.languages_id', '=', $formInput['language_id'])
				->where(function ($query) use ($formInput) {
					$query->where(function ($query) use ($formInput) {
						$query->where('conf_attendees_info.name', '=', 'network_group')
							->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
					})
						->orWhere(function ($query) use ($formInput) {
							$query->where('conf_attendees_info.name', '=', 'delegate_number')
								->where('conf_attendees_info.value', 'LIKE', '%' . trim($formInput['query']) . '%');
						})
						->orWhere(\DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', '%' . trim($formInput['query']) . '%')
						->orWhere('email', 'LIKE', '%' . trim($formInput['query']) . '%');
				});
		}
		$attendees = $attendee->groupBy('conf_attendees.id')->orderBy('first_name')->paginate($formInput['limit']);

		// display Setting refactor
		foreach ($attendees as $key => $value) {
			if ($value->currentEventAttendee->speaker == 1) {
				$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'speaker', $lang_id);
				$value = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $value, $attendee_type_id);
			} elseif ($value->currentEventAttendee->exhibitor == 1) {
				$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'exhibitor', $lang_id);
				$value = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $value, $attendee_type_id);
			} elseif ($value->currentEventAttendee->sponser == 1) {
				$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'sponsor', $lang_id);
				$value = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $value, $attendee_type_id);
			} else {
				$value = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $value, $value->currentEventAttendee->attendee_type);
			}
			$value->gdpr = $formInput['event']['gdpr_settings']['enable_gdpr'] ? $value->currentEventAttendee->gdpr : 1;
			$attendees[$key] = $value;
		}

		return $attendees;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function getSpeakerSetting($formInput)
	{
		return \App\Models\SpeakerSetting::where('event_id', $formInput['event_id'])->first();
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function getSponsorSetting($event_id)
	{
		return \App\Models\SponsorSetting::where('event_id', $event_id)->first();
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function getExhibitorSetting($event_id)
	{
		return \App\Models\ExhibitorSetting::where('event_id', $event_id)->first();
	}

	/**
	 * @param mixed $info
	 * @param string $type
	 * @param mixed $settings
	 * 
	 * @return [type]
	 */
	public static function refactorAttendeeInfo($info, $settings)
	{	
		foreach ($info as $key => $item) {
			$item_name = is_array($item) ? $item['name'] : $item->name;
			if (isset($settings[$item_name])) {
				if ($settings[$item_name] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'industry') {
				if ($settings['show_industry'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'jobs') {
				if ($settings['show_job_tasks'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'interests') {
				if ($settings['interest'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'about') {
				if ($settings['bio_info'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'private_street') {
				if ($settings['pa_street'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'private_house_number') {
				if ($settings['pa_house_no'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'private_post_code') {
				if ($settings['pa_post_code'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'private_city') {
				if ($settings['pa_city'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			} else if($item_name == 'private_country') {
				if ($settings['pa_country'] !== 1) {
					if(is_array($item)) {
						$item['value'] = "";
					} else {
						$item->value = "";
					}
				}
			}
			$info[$key] = $item;
		}
		return $info;
	}

	/**
	 * @param mixed $formInput
	 * @param integer $id
	 * 
	 * @return [mixed]
	 */
	public function getEventSpeaker($formInput, $id)
	{
		$lang_id = $formInput['language_id'];

		$speakerPrograms = \App\Models\EventAgendaSpeaker::where('event_id', '=', $formInput['event_id'])->where('attendee_id', '=', $id)->with(['eventProgram.info' => function ($query) use ($lang_id) {
			return $query->where('languages_id', '=', $lang_id);
		}, 'eventProgram.program_speakers.info', 'eventProgram.program_speakers.currentEventAttendee', 'eventProgram.program_workshop.info' => function ($query) use ($formInput) {
			return $query->where('languages_id', $formInput['language_id']);
		}, 'eventProgram.tracks.info' => function ($query) use ($formInput) {
			return $query->where('languages_id', $formInput['language_id']);
		}])->get()->toArray();

		$programs = array();

		foreach ($speakerPrograms as $key => $program) {
			$programs[] = $program['event_program'];
		}

		foreach ($programs as $key => $row) {

			$rowData = array();

			$infoData = readArrayKey($row, $rowData, 'info');

			$rowData['id'] = $row['id'];

			$rowData['workshop_id'] = $row['workshop_id'];

			$rowData['topic'] = isset($infoData['topic']) ? $infoData['topic'] : '';

			$rowData['description'] = isset($infoData['description']) ? $infoData['description'] : '';

			$rowData['date'] = isset($infoData['date']) ? date('Y-m-d', strtotime($infoData['date'])) : '';

			$rowData['heading_date'] = $rowData['date'];

			$rowData['start_time'] = isset($infoData['start_time']) ? $infoData['start_time'] : '';

			$rowData['end_time'] = isset($infoData['end_time']) ? $infoData['end_time'] : '';

			$rowData['location'] = isset($infoData['location']) ? $infoData['location'] : '';

			//program speakers
			$program_speakers = array();

			if (count($row['program_speakers'] ?? []) > 0) {
				foreach ($row['program_speakers'] as $speaker) {
					$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'speaker', $lang_id);
					$speaker = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $speaker, $attendee_type_id, $speaker["current_event_attendee"]["gdpr"]);
					$speaker['info'] = readArrayKey(['info' => $speaker['info']], [], 'info');
					$program_speakers[] = $speaker;
				}
			}

			$rowData['program_speakers'] = $program_speakers;

			// program workshop
			if (count($row['program_workshop']['info'] ?? []) > 0) {
				foreach ($row['program_workshop']['info'] as $val) {
					if ($val['name'] == "name") {
						$rowData['program_workshop'] = $val['value'];
					}
				}
			}

			//program tracks
			$program_tracks = array();
			if (count($row['tracks'] ?? []) > 0) {
				foreach ($row['tracks'] as $track) {
					$info = readArrayKey($track, [], 'info');
					$program_tracks[] = $info;
				}
			}

			//Workshop programs
			$formInput['workshop_id'] = $row['workshop_id'];
			
			$rowData['program_tracks'] = $program_tracks;

			$programs['data'][$key] = $rowData;
		}


		$data['data'] = collect($programs['data'], "date")->groupBy('date')->all();

		$skip_workshops = array();

		$programs = array();

		foreach($data['data'] as $date => $program) {
            foreach ($program as $key => $row) {
                if ($row['workshop_id'] > 0) {
                    if (!in_array($row['workshop_id'], $skip_workshops)) {
						$formInput['workshop_id'] = $row['workshop_id'];
						$formInput['speaker_id'] = $id;
                        $workshop_programs = ProgramRepository::workshopPrograms($formInput);
                        $row['workshop_programs'] = $workshop_programs;
                        $programs[$date][] = $row;
                        $skip_workshops[] = $row['workshop_id'];
                    }
                }else{
                    $programs[$date][]=$row;
                }
            }
        }

		$speaker = Attendee::selectRaw('conf_attendees.*, conf_event_data_log_session.attendee_id AS online_id')
			->join('conf_event_agenda_speakers', 'conf_event_agenda_speakers.attendee_id', '=', 'conf_attendees.id')
			->leftJoin('conf_event_data_log_session', 'conf_event_data_log_session.attendee_id', '=', 'conf_attendees.id')
			->where('conf_event_agenda_speakers.event_id', $formInput['event_id'])
			->where('conf_attendees.id', $id)
			->whereNull('conf_attendees.deleted_at')
			->whereNull('conf_event_agenda_speakers.deleted_at')
			->with(['info' => function ($q) use ($lang_id) {
				$q->where('languages_id', $lang_id);
		}, 'currentEventAttendee']);

		if ($formInput['event']['gdpr_settings']['enable_gdpr'] && $formInput['event']['gdpr_settings']['attendee_invisible']) {
			$speaker->whereHas('currentEventAttendee', function ($query) {
				$query->where('gdpr', '=', '1');
			});
		}

		$speaker = $speaker->first();

		if (!$speaker) {
			return null;
		}

		$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'speaker', $lang_id);

		$speaker = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $speaker, $attendee_type_id, $speaker->currentEventAttendee->gdpr);

		$speaker->info = readArrayKey(['info' => $speaker->info], [], 'info');
		
		$speaker["programs"] = $programs;
		
		if(is_object($speaker)) {
			$relations = $speaker->getRelations();
			unset($relations['info']);
			$speaker->setRelations($relations);
		}

		return $speaker;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public static function removeEmptyKeys($arr)
	{
		$newArr = [];
		foreach ($arr as $val) {
			if ($val['value'] !== "") {
				$newArr[$val['name']] = $val['value'];
			}
		}
		return $newArr;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function getFrontEventAttendee($formInput, $id)
	{
		$lang_id = $formInput['language_id'];

		$attendee = Attendee::select(['conf_attendees.*', 'conf_event_data_log_session.attendee_id AS online_id'])
			->join('conf_event_attendees', 'conf_event_attendees.attendee_id', '=', 'conf_attendees.id')
			->leftJoin('conf_event_data_log_session', 'conf_event_data_log_session.attendee_id', '=', 'conf_attendees.id')
			->where('conf_event_attendees.event_id', $formInput['event_id'])
			->where("conf_attendees.id", $id)
			->whereNull('conf_attendees.deleted_at')
			->whereNull('conf_event_attendees.deleted_at')
			->with(['info' => function ($q) use ($lang_id) {
				$q->where('languages_id', $lang_id);
			}, 'currentEventAttendee']);

		if ($formInput['event']['gdpr_settings']['enable_gdpr'] && $formInput['event']['gdpr_settings']['attendee_invisible']) {
			$attendee->whereHas('currentEventAttendee', function ($query) {
				$query->where('gdpr', '=', '1');
			});
		}

		$attendee = $attendee->first();

		if ($attendee->currentEventAttendee->speaker == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'speaker', $lang_id);
			$attendee = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $attendee, $attendee_type_id);
		} elseif ($attendee->currentEventAttendee->exhibitor == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'exhibitor', $lang_id);
			$attendee = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $attendee, $attendee_type_id);
		} elseif ($attendee->currentEventAttendee->sponser == 1) {
			$attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($formInput['event_id'], 'sponsor', $lang_id);
			$attendee = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $attendee, $attendee_type_id);
		} else {
			$attendee = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput['event_id'], $attendee, $attendee->currentEventAttendee->attendee_type);
		}

		$attendee->gdpr = $formInput['event']['gdpr_settings']['enable_gdpr'] ? $attendee->currentEventAttendee->gdpr : 1;

		return $attendee;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function getFrontEventAttendeeProfile($formInput, $id)
	{
		$profile = array();

		$lang_id = $formInput['language_id'];

		$attendee_feild_settings = \App\Models\AttendeeFieldSetting::where('event_id', '=', $formInput["event_id"])->first()->toArray();

		$attendee = Attendee::where('id', '=', $id)->with(['info' => function ($q) use ($lang_id) {
			return $q->where('languages_id', '=', $lang_id);
		}, 'currentEventAttendee', 'currentEventAttendee.regForm'])->first();
		
		if(!isset($formInput['is_edit'])){
			$attendee = AttendeeRepository::refactorDataByAttendeeTypeSettings($formInput["event_id"], $attendee, $attendee->currentEventAttendee->attendee_type);
		}
		
		$attendee = $attendee->toArray();

		$attendee["info"] = readArrayKey($attendee, [], 'info');

		$attendee['countryName'] = getCountryName($attendee['info']['country']);

		if(isset($attendee["info"]['private_country']) && $attendee["info"]['private_country']) {
			$attendee["info"]['private_country_name'] = getCountryName($attendee["info"]['private_country']);
		}
	 
		$callingCodes = get_all_country_codes();

        $languages = get_all_countries_languages();

        $countries = get_all_countries();
		
		$event_language_ids = get_event_languages($formInput['event_id']);

		$event_language_details = Language::whereIn('id', $event_language_ids)->get()->toArray();
		
		$event_food_disclaimers = EventFoodAllergies::where('event_id', $formInput['event_id'])->get()->toArray();

		$customFields = \App\Models\EventCustomField::where('event_id', '=', $formInput['event_id'])
			->where('registration_form_id', $attendee['current_event_attendee']['reg_form']['id'])
            ->with(['info' => function ($query) use ($lang_id) {
                return $query->where('languages_id', '=', $lang_id);
            }, 'childrenRecursive' => function ($r) {

                return $r->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC');
            }, 'childrenRecursive.info' => function ($query) use ($lang_id) {
                return $query->where('languages_id', '=', $lang_id);
            }])->where('parent_id', '=', '0')->orderBy('sort_order', 'ASC')->get()->toArray();
		
		$customFields = returnInfoRecursiveChild($customFields, 'children_recursive');
		
			
		foreach ($customFields as $key => $value) {
			foreach ($value['info'] as $key2 => $info) {
				$value[$info['name']] = $info['value'];
			}

			unset($value['info']);

			foreach ($value['children_recursive'] as $key3 => $children) {
				foreach ($children['info'] as $key4 => $info) {
					$children[$info['name']] = $info['value'];
				}
				unset($children['info']);
				$value['children_recursive'][$key3] = $children;
			}

			$customFields[$key] = $value;
		}

		$order = EventsiteBillingOrderRepository::getOrderForMainAttendee($formInput['event_id'], $id);

		$enable_cancel = EventsiteRepository::getCancelStatus($order, $formInput["event"]['eventsiteSettings']);

		$profile = [
			"attendee" => $attendee,
			"countries" => $countries,
			"event_language_details" => $event_language_details,
			"callingCodes" => $callingCodes,
			"event_food_disclaimers" => $event_food_disclaimers,
			"attendee_feild_settings" => $attendee_feild_settings,
			"customFields" => $customFields,
			"languages" => $languages,		
			"enable_cancel" => $enable_cancel,	
			"order_attendee_count" => $order ? count($order->order_attendees): 0
		];

		return $profile;
	}
	
	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function getNewsletterSubscription($formInput, $id)
	{
        $event_id = $formInput['event_id'];
        $newsLetterSubscriberSettings = \App\Models\EventNewsSubscriberSetting::where('event_id', '=', $event_id)->first();
		$attendee = Attendee::where('id', $id)->first();
		$subscriberList = json_decode($newsLetterSubscriberSettings->subscriber_ids, true);
        $subscriber_detail = ['status' => 0, 'subscriber_list' => []];
		$resultList = [];
        $ids = [];
		if($newsLetterSubscriberSettings) {
            $subscriber_detail['status'] = $newsLetterSubscriberSettings->status;
            $subscriberList = json_decode($newsLetterSubscriberSettings->subscriber_ids, true);
            if(count($subscriberList) > 0) {
                $subscriberListIds = array_column($subscriberList, 'id');
                $mailingList = \App\Models\MailingList::where('organizer_id','=',$formInput['organizer_id'])->whereIn('id', $subscriberListIds)->get();
                foreach ($mailingList as $list) {
                    $key = array_search($list->id, $subscriberListIds);
                    $status = false;
                    $attendee_subscription = \App\Models\MailingListSubscriber::where('email',$attendee['email'])->where('mailing_list_id',$list->id)->whereNull('unsubscribed')->whereNull('deleted_at')->get();
                    if(count($attendee_subscription) != 0){
                        $status = true;
                        $ids[$list->id] =  $list->id;
                    }
                    $resultList[] = [
                        'id' => $list->id,
                        'name' => $subscriberList[$key]['label'],
                        'isExists' => $status
                    ];
                }
                $subscriber_detail['subscriber_list'] = $resultList;
            }
        }		
		return [
				"newsLetterSubscriberSettings"=>$newsLetterSubscriberSettings,
				"subscriber_detail"=>$subscriber_detail,
				"previous_ids"=>$ids,
			   ];
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function updateNewsletterSubscription($formInput, $id)
	{
        $event_id = $formInput['event_id'];
        $previous_ids = $formInput['previous_ids'];
        $latest_ids = $formInput['subscriber_ids'];
        $organizer_id = $formInput['organizer_id'];
		$attendee = \App\Models\Attendee::where('id', $id)->first();
        $email = $attendee['email'];
        $first_name = $attendee['first_name'];
        $last_name = $attendee['last_name'];

        foreach ($latest_ids as $ids){
            $mailing = \App\Models\MailingListSubscriber::where('email',$email)->where('mailing_list_id',$ids)->where('event_id',$event_id)->whereNull('deleted_at')->get()->first();
            if(!$mailing){
                $mailing = new  \App\Models\MailingListSubscriber();
                $mailing->mailing_list_id = $ids;
                $mailing->organizer_id = $organizer_id;
                $mailing->event_id = $event_id;
                $mailing->email = $email;
                $mailing->first_name = $first_name;
                $mailing->last_name = $last_name;
                $mailing->created_at = date("Y-m-d H:i:s");
                $mailing->updated_at = date("Y-m-d H:i:s");
                $mailing->save();
            }
            if(!in_array($ids,$previous_ids)){
                $mailing->updated_at = date("Y-m-d H:i:s");
                $mailing->unsubscribed = null;
                $mailing->save();
            }
        }

        foreach ($previous_ids as $ids){
            if(!in_array($ids,$latest_ids)){
                $mailing = \App\Models\MailingListSubscriber::where('email',$email)->where('mailing_list_id',$ids)->where('event_id',$event_id)->whereNull('deleted_at')->get()->first();
                $mailing->unsubscribed =  date("Y-m-d H:i:s");
                $mailing->updated_at = date("Y-m-d H:i:s");
                $mailing->save();
            }
        }

		return true;
	}
	
	public function checkifSendEmail($formInput, $attendee_id) {
        $event_id = $formInput['event_id'];

        $subregistration = \App\Models\EventSubRegistration::where('event_id','=', $event_id)->first()->toArray();
        $subregistration_questions = \App\Models\EventSubRegistrationQuestion::where('sub_registration_id', '=', $subregistration['id'])
            ->get()->toArray();
        //echo "<pre>"; print_r($new_input); echo "<hr>";
        $send_email = 'no';
        foreach ($subregistration_questions as $question) {
            $results = \App\Models\EventSubRegistrationResult::where('question_id', '=', $question['id'])->where('attendee_id', '=', $attendee_id)
                ->get()->toArray();
            if (count($results) > 0) {
                if ($question['question_type'] == 'single') {
                    if ($results[0]['answer_id'] != $formInput['answer'.$question['id']][0]) {
                        if ($question['enable_comments'] == 1) {
                            if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                                $send_email = 'yes';
                                break;
                            }
                        } else {
                            $send_email = 'yes';
                            break;
                        }
                    } elseif ($question['enable_comments'] == 1) {
                        if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                            $send_email = 'yes';
                            break;
                        }
                    }
                } elseif ($question['question_type'] == 'open') {
                    if ($results[0]['answer'] != $formInput['answer_open'.$question['id']][0]) {
                        if ($question['enable_comments'] == 1) {
                            if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                                $send_email = 'yes';
                                break;
                            }
                        } else {
                            $send_email = 'yes';
                            break;
                        }
                    } elseif ($question['enable_comments'] == 1) {
                        if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                            $send_email = 'yes';
                            break;
                        }
                    }

                } elseif ($question['question_type'] == 'number') {
                    if ($results[0]['answer'] != $formInput['answer_number'.$question['id']][0]) {
                        if ($question['enable_comments'] == 1) {
                            if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                                $send_email = 'yes';
                                break;
                            }
                        } else {
                            $send_email = 'yes';
                            break;
                        }
                    } elseif ($question['enable_comments'] == 1) {
                        if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                            $send_email = 'yes';
                            break;
                        }
                    }

                } elseif ($question['question_type'] == 'date') {
                    if ($results[0]['answer'] != $formInput['answer_date'.$question['id']][0]) {
                        if ($question['enable_comments'] == 1) {
                            if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                                $send_email = 'yes';
                                break;
                            }
                        } else {
                            $send_email = 'yes';
                            break;
                        }
                    } elseif ($question['enable_comments'] == 1) {
                        if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                            $send_email = 'yes';
                            break;
                        }
                    }

                } elseif ($question['question_type'] == 'date_time') {
                    if ($results[0]['answer'] != $formInput['answer_date_time'.$question['id']][0]) {
                        if ($question['enable_comments'] == 1) {
                            if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                                $send_email = 'yes';
                                break;
                            }
                        } else {
                            $send_email = 'yes';
                            break;
                        }
                    } elseif ($question['enable_comments'] == 1) {
                        if ($results[0]['comments'] != $formInput['comments'.$question['id']]) {
                            $send_email = 'yes';
                            break;
                        }
                    }


                } elseif  ($question['question_type'] == 'multiple') {
                    //print_all($results);
                    if (count($results) != count($formInput['answer'.$question['id']])) {
                        $send_email = 'yes';
                        break;
                    } else {
                        foreach ($results as $result) {
                            if (!in_array($result['answer_id'], $formInput['answer'.$question['id']])) {
                                $send_email = 'yes';
                                break;
                            }
                        }
                    }
                    if ($question['enable_comments'] == 1) {
                        foreach ($formInput['answer'.$question['id']] as $comment) {
                            if ($result['comment'] != $formInput['comments'.$question['id']]) {
                                $send_email = 'yes';
                                break;
                            }
                        }
                    }


                } elseif  ($question['question_type'] == 'dropdown') {
                    //print_all($results);
                    if (count($results) != count($formInput['answer'.$question['id']])) {
                        $send_email = 'yes';
                        break;
                    } else {
                        foreach ($results as $result) {
                            if (!in_array($result['answer_id'], $formInput['answer'.$question['id']])) {
                                $send_email = 'yes';
                                break;
                            }
                        }
                    }
                    if ($question['enable_comments'] == 1) {
                        foreach ($formInput['answer'.$question['id']] as $comment) {
                            if ($result['comment'] != $formInput['comments'.$question['id']]) {
                                $send_email = 'yes';
                                break;
                            }
                        }
                    }
                }


            }
        }
        return $send_email;
    }

	public function getbillingProfile($formInput, $attendee_id)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];
        //Login Check

        if ($_POST['registration'] == 'true') {
            if ($this->validateRegistration($formInput)) {
                // Basic Info Columns
                $column_record = \DB::select(\DB::raw("SHOW COLUMNS FROM conf_attendees"));
                $column_record = object_to_array($column_record);
                foreach ($column_record as $row_columns) {
                    $array_column[] = $row_columns['Field'];
                }
                //Update Basic Info
                $basic_sql = "UPDATE conf_attendees SET";
                $i = 0;
                foreach ($array_column as $column) {
                    if ((isset($_POST[$column]) && $column != 'email') || ($column == 'delegate_number' || $column == 'table_number')) {
                        $value = $_POST[$column];
                        if ($column == 'delegate_number') {
                            $value = $_POST['delegate'];
                        }
                        if ($column == 'table_number') {
                            $value = $_POST['tblNumber'];
                        }
                        $key = $column;
                        if ($i == 0) {
                            $basic_sql .= " `" . $key . "` = '" . addslashes($value) . "'";
                        } else {
                            $basic_sql .= ", `" . $key . "` = '" . addslashes($value) . "'";
                        }
                        $i++;
                    }
                }
                $basic_sql .= " WHERE id  = '" . $attendee_id . "'";
                \DB::update(\DB::raw($basic_sql));
                $column_record = \DB::select(\DB::raw("SELECT * FROM conf_attendees_info GROUP BY name"));
                $column_record = object_to_array($column_record);
                foreach ($column_record as $row_columns) {
                    $array_column_info[] = $row_columns['name'];
                }
                $i = 0;
                foreach ($array_column_info as $column) {
                    if ((isset($_POST[$column]) && $column != 'email') || ($column == 'delegate_number' || $column == 'table_number')) {
                        $value = $_POST[$column];
                        if ($column == 'delegate_number') {
                            $value = $_POST['delegate'];
                        }
                        if ($column == 'table_number') {
                            $value = $_POST['tblNumber'];
                        }
                        $key = $column;
                        $i++;
                    }
                    if ($column == 'custom_field_id' . $event_id) {
                        if (isset($_POST['custom_field_id'])) {
                            $value =  implode(',',$_POST['custom_field_id']);
                            $key = $column;
                            $i++;
                        }
                    }
                    //Update Attendee Info Table
                    $attendee_info_sql = "UPDATE conf_attendees_info SET value = '" . $value . "' WHERE name = '" . $key . "' and attendee_id  = '" . $attendee_id . "' and languages_id = " . $lang_id;
                    \DB::update(\DB::raw($attendee_info_sql));
                }
                //Update Billing Info
                $billing_sql = "UPDATE conf_attendee_billing SET `billing_private_street` = '" . addslashes($_POST['private_street']) . "'";

                $result = \App\Models\BillingField::where('type', '=', 'section')->where('event_id', '=', $event_id)->where('status', '=', '1')->with(['info' => function ($query) use($lang_id) {
                    return $query->where('languages_id', '=', $lang_id);
                }])->orderBy('sort_order', 'ASC')->get()->toArray();
                $result = returnInfoArray($result);
                foreach ($result as $row) {
                    //Fields
                    $q_fields = \App\Models\BillingField::where('type', '=', 'field')->where('event_id', '=', $event_id)->where('section_alias', '=', $row['field_alias'])->where('status', '=', '1')->with(['info' => function ($query) use($lang_id) {
                        return $query->where('languages_id', '=', $lang_id);
                    }])->orderBy('sort_order', 'ASC')->get()->toArray();
                    $q_fields = returnInfoArray($q_fields);
                    foreach ($q_fields as $rs_fields) {
                        $posted_data[$rs_fields['field_alias']] = $_POST[$rs_fields['field_alias']];
                    }

                }
                $special_column_array = array('attendee_type','country','credit_card_payment', 'company_name', 'department', 'confirm_password', 'delegate', 'tblNumber', 'company_private_payment', 'passport_no','place_of_birth', 'date_of_issue_passport', 'date_of_expiry_passport', 'private_house_number', 'private_street', 'private_post_code', 'private_city', 'private_country', 'company_public_payment', 'company_invoice_payment', 'initial', 'custom_field_id', 'table_number', 'network_group', 'age', 'gender', 'organization', 'jobs', 'interests', 'title', 'industry', 'about', 'phone');
                foreach ($posted_data as $key => $data) {
                    if (!in_array($key, $array_column) && !in_array($key, $special_column_array)) {
                        $billing_sql .= ", `billing_" . $key . "` = '" . addslashes($data) . "'";
                    }
                }
                $billing_sql .= " WHERE event_id = " . $event_id . " AND attendee_id = " . $attendee_id;
                \DB::update(\DB::raw($billing_sql));
            }
        }


        $eventsite_MyProfile = \DB::select(\DB::raw("SELECT * FROM conf_event_site_text WHERE event_id  = '" . $event_id . "' AND alias ='EVENTSITE_MY_PROFILE' "));
        $eventsite_MyProfileResult = object_to_array($eventsite_MyProfile);
        $eventsite_MyProfileResult = $eventsite_MyProfileResult[0];
        $myProfile = $eventsite_MyProfileResult['value'];
        $event_setting = \DB::select(\DB::raw("SELECT * FROM conf_event_settings WHERE event_id = '" . $event_id . "'"));
        $setting = object_to_array($event_setting);
        $setting = $setting[0];
        $subregistration_list = $setting['eventsite_subregistration_list'];
        $sub_registration_enabled = '';
        if ($subregistration_list == 'b') {
            $sub_registration_enabled = 'true';
        }
        $site_areas = array();
        $result = \DB::select(\DB::raw("SELECT * FROM conf_event_site_areas WHERE event_id = '" . $event_id . "' ORDER BY  sort_order ASC, name"));
        $result = object_to_array($result);
        foreach ($result as $row) {
            $site_areas[] = $row;
        }
        //Sections
        $result = \App\Models\BillingField::where('type', '=', 'section')->where('event_id', '=', $event_id)->where('status', '=', '1')->with(['info' => function ($query) use($lang_id) {
            return $query->where('languages_id', '=', $lang_id);
        }])->orderBy('sort_order', 'ASC')->get()->toArray();
        $result = returnInfoArray($result);
        foreach ($result as $row) {
            //Fields
            $q_fields = \App\Models\BillingField::where('type', '=', 'field')->where('event_id', '=', $event_id)->where('section_alias', '=', $row['field_alias'])->where('status', '=', '1')->with(['info' => function ($query) use($lang_id) {
                return $query->where('languages_id', '=', $lang_id);
            }])->orderBy('sort_order', 'ASC')->get()->toArray();
            $q_fields = returnInfoArray($q_fields);
            foreach ($q_fields as $rs_fields) {
                $row['fields'][] = $rs_fields;
            }
            $sections[] = $row;
        }
        $is_mobile = 'No';
        //conf_attendee_billing
        $billing_result = \DB::select(\DB::raw("SELECT * FROM conf_attendee_billing WHERE event_id  = '" . $event_id . "' AND attendee_id = '" . $attendee_id . "' Order by id desc"));
        $attendee_billing_info = object_to_array($billing_result);
        $attendee_billing_info = $attendee_billing_info[0];
        //$attendee_basic_info
        $basic_result = \App\Models\Attendee::where('id', '=', $attendee_id)->with(['info' => function ($query) use($lang_id) {
            return $query->where('languages_id', '=', $lang_id);
        }])->get()->toArray();
        $attendee_basic_info = returnInfoArray($basic_result);
        $attendee_basic_info = $attendee_basic_info[0];
        $allCountries = get_all_countries();
        $labels_array = array('SPOKEN_LANGUAGE', 'EMPLOYMENT_DATE', 'BIRTHDAY_YEAR', 'FIRST_NAME_PASSPORT', 'LAST_NAME_PASSPORT');
        $allCountryLanguages = get_all_languages();
        $year = date('Y');
        $years = array();
        for ($i = $year; $i >= 1920; $i--) {
            $years[] = $i;
        }
        $custom_fields = getCustomFields($event_id, $lang_id);
        $custom_fields = returnInfoArray($custom_fields);
        // TODO: Eventsettings OBJECT

		return [
			'custom_fields' => $custom_fields, 
			'years' => $year, 
			'allCountryLanguages' => $allCountryLanguages, 
			'is_mobile' => $is_mobile, 
			'attendee_billing_info' => $attendee_billing_info, 
			'attendee_basic_info' => $attendee_basic_info, 
			'sections' => $sections, 
			'allCountries' => $allCountries, 
			'myProfile'=> $myProfile
		];

    }

	public function validateRegistration($formInput)
    {
		$eventSiteLabels= $formInput['event']['labels'];
        $errors = array();
        $firstEmailCheck = false;
        $organizer_id = $formInput['organizer_id'];
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];

        //Sections
        //$result = \DB::select(\DB::raw("SELECT * FROM conf_billing_fields WHERE event_id  = '".$event_id."' AND type = 'section' AND `status`= 1 ORDER BY  sort_order ASC"));
        $result = \App\Models\BillingField::where('type', '=', 'section')->where('event_id', '=', $event_id)->where('status', '=', '1')->with(['info' => function ($query) use($lang_id) {
            return $query->where('languages_id', '=', $lang_id);
        }])->orderBy('sort_order', 'ASC')->get()->toArray();
        $result = returnInfoArray($result);
        foreach ($result as $row) {
            //Fields
            //$q_fields = \DB::select(\DB::raw("SELECT * FROM conf_billing_fields WHERE event_id  = '".$event_id."' AND type = 'field'  AND section_alias = '".$row['field_alias']."' AND `status`= 1  ORDER BY  sort_order ASC"));
            $q_fields = \App\Models\BillingField::where('type', '=', 'field')->where('event_id', '=', $event_id)->where('section_alias', '=', $row['field_alias'])->where('status', '=', '1')->with(['info' => function ($query) use($lang_id) {
                return $query->where('languages_id', '=', $lang_id);
            }])->orderBy('sort_order', 'ASC')->get()->toArray();
            $q_fields = returnInfoArray($q_fields);

            foreach ($q_fields as $rs_fields) {
                $row['fields'][] = $rs_fields;
            }

            $sections[] = $row;

        }

        $p_event_id = 1179;
        if (count($sections)) {
            foreach ($sections as $row) {
                foreach ($row['fields'] as $field) {
                    if ($field['field_alias'] == 'company_public_payment' || $field['field_alias'] == 'company_invoice_payment' || $field['field_alias'] == 'credit_card_payment') {
                        continue;
                    }
                    if ($field['mandatory'] == 1) {

                        if (trim($_POST[$field['field_alias']]) == '' && $field['field_alias'] != 'custom_field_id') {

                            if ($field['field_alias'] == 'site_area_id') {
                                $error_start = $eventSiteLabels["REGISTRATION_ERROR_DROP_DOWN"];
                            } else {
                                $error_start = $eventSiteLabels["REGISTRATION_ERROR"];
                            }

                            if ($field['field_alias'] == 'interests' && ($p_event_id == $event_id)) {
                                $error_start = $eventSiteLabels["REGISTRATION_ERROR_DROP_DOWN"];
                            }

                            if (($_POST['member'] != '1' && $field['field_alias'] == 'member_number') || $field['field_alias'] == 'password' || $field['field_alias'] == 'confirm_password' || ($_POST['company_type'] != 'public' && $field['field_alias'] == 'ean')) {

                            } else {
                                $errors[] = $error_start . " " . strtolower($field['name']);
                            }
                        }


                        if ($field['field_alias'] == 'custom_field_id') {

                            if (count($_POST[$field['field_alias']]) == 0) {
                                $errors[] = $error_start . " " . strtolower($field['name']);
                            }

                        }


                    }
                }
            }
        }


        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = $eventSiteLabels["EMAIL_REQUIRED"];
        }

        //check Registration Date
        $result_detail = \DB::select(\DB::raw("SELECT tickets_left, registration_end_date FROM conf_events WHERE organizer_id = '" . $organizer_id . "' AND id = '" . $event_id . "'"));
        $eventDetail = object_to_array($result_detail);
        $eventDetail = $eventDetail[0];

        if ($eventDetail['registration_end_date'] <> '0000-00-00 00:00:00') {
            $currentDate = strtotime(date('Y-m-d', time()));
            $startDate = strtotime($eventDetail['registration_end_date']);
            if ($currentDate > $startDate) {
                $errors[] = $eventSiteLabels["REGISTER_DATE_END"];
            }


        }

        if ($eventDetail['ticket_left'] > 0 && $eventDetail['ticket_left'] <> '') {
            //total attendees allowed
            $query = \DB::select(\DB::raw("SELECT COUNT(*) as totalAttendees FROM conf_attendees a
                 INNER JOIN `conf_event_attendees` ea  ON ea.attendee_id = a.id
                 AND ea.event_id = " . $event_id . "
                 WHERE a.organizer_id = '" . $organizer_id . "' AND ea.deleted_at is null"));

            $attendee_count = object_to_array($query);
            $attendee_count = $attendee_count = [0];
            if ($eventDetail['ticket_left'] <= $attendee_count['totalAttendees']) {
                $errors[] = $eventSiteLabels["REGISTER_TICKET_END"];
            }
        }

        //validate domain
        $event_domain = \DB::select(\DB::raw("SELECT * FROM conf_events WHERE id ='" . $event_id . "' "));
        $domain_result = object_to_array($event_domain);
        $domain_result = $domain_result[0];
        $domain_name = strtolower($domain_result['domain_name']);

        $error_message_query = \DB::select(\DB::raw("SELECT * FROM conf_event_site_text WHERE event_id  = '" . $event_id . "' AND section_order = '10' AND (alias='REGISTRATION_DOMAIN_VALIDATION_ERROR')"));
        $error_message_query = object_to_array($error_message_query);
        if (count($error_message_query) > 0) {
            $error_msg_query_result = $error_message_query[0];
            $reg_domain_validation_error = $error_msg_query_result['value'];
        } else {
            $reg_domain_validation_error = $eventSiteLabels["REGISTER_VALID_DOMAIN"];
        }

        if ($domain_name != '') {
            $domain_data = explode("@", $_POST['email']);
            $domain_data[1] = strtolower($domain_data[1]);

            if ($domain_name != $domain_data[1]) {
                $errors[] = $reg_domain_validation_error;
            }
        }

        if (count($errors) == 0) {
            return true;
        } else {
            return false;
        }
    }

	public function getBillingHistory($formInput, $slug, $attendee_id){
		$event_id=$formInput['event_id'];
		$billing_history = '';
        $order_detail = $this->checkbillingOrder($event_id, $attendee_id);
        if ($order_detail['id']) {
            $billing_history = $this->order_detail($slug, $order_detail, $formInput);
        }
		return [
			"order_detail" => $order_detail,
			"billing_history" => $billing_history
		];
	}

	function order_detail($event_url, $order_detail, $formInput ) {

		$id = $order_detail['id'];
        //Basic set up
        $order_id = $id;

        $eventsite_setting = $formInput["event"]['eventsiteSettings'];

		$lang_id = $formInput['language_id'];

		$event_id = $formInput['event_id'];

        if ( $eventsite_setting['third_party_redirect_url'] != '' && (parse_url($eventsite_setting['third_party_redirect'], PHP_URL_SCHEME) === null)) {
            $eventsite_setting['third_party_redirect_url'] = "http://" . $eventsite_setting['third_party_redirect_url'];
        }

        $result = \DB::select(\DB::raw("SELECT * FROM conf_billing_orders WHERE id = '$id'"));

        $result = object_to_array($result);

        $order = $result[0];

        $questions = $this->getSubRegistrationResults($event_id, $order['attendee_id']);

        $temp_modules = \App\Models\EventModuleOrder::where('event_id', '=', $event_url)->where('is_purchased', '=', '1')->where('alias', '=', 'subregistration')->where('type', '=', 'backend')->where('status', '=', '1')->get()->toArray();
        
        return [
			'html' => 'Need order invoice html here', 
			'questions' => $questions, 
			'order_id' => $order_id, 
			'temp_modules' => $temp_modules, 
			'order_detail' => $order_detail
		];
   
	}

	public function checkbillingOrder($event_id, $attendee_id)
    {
        // Check SMS Module
        $billing_query = \DB::select(\DB::raw("SELECT * FROM conf_billing_orders WHERE attendee_id = ".$attendee_id." AND event_id = ".$event_id." AND status<>'cancelled' AND status<>'rejected' AND is_archive = 0 ORDER BY id Desc Limit 1"));
        $result = object_to_array($billing_query);
        $result = $result[0];
        return $result;
    }

	public function getSubRegistrationResults($event_id, $attendee_id)
    {
        $polls = array();
        $pollresults = \DB::select(\DB::raw("SELECT * FROM conf_event_sub_registration WHERE event_id = " . $event_id." AND deleted_at IS NULL"));
        $pollresults = object_to_array($pollresults);
        $poll = $pollresults['0'];
        if ($poll['id']) {
            $results = \DB::select(\DB::raw("SELECT q.*,info.value as question_name FROM conf_event_sub_registration_questions q
                INNER JOIN conf_event_sub_registration_question_info info on info.question_id = q.id
                WHERE q.sub_registration_id = " . $poll['id'] . " AND q.status = 1 AND q.deleted_at IS NULL  ORDER BY sort_order ASC, q.id DESC"));
            $results  = object_to_array($results);

            foreach ($results as $row) {
                $presults = \DB::select(\DB::raw("SELECT * FROM conf_event_sub_registration_results WHERE question_id = " . $row['id'] . " AND event_id = '" . $event_id . "' AND attendee_id = '" . $attendee_id . "'"));

                if (count($presults) == 0) {
                    if ($row['question_type'] != '3') {
                        $answers = array();
                        $r = \DB::select(\DB::raw("SELECT conf_event_sub_registration_answers.*,info.value as answer FROM conf_event_sub_registration_answers
                          INNER JOIN conf_event_sub_registration_answer_info info ON info.answer_id = conf_event_sub_registration_answers.id
                          WHERE question_id = " . $row['id'] . " ORDER BY sort_order ASC"));
                        foreach ($r as $ans) {
                            $answers[] = $ans;
                        }
                        $row['answers'] = $answers;
                        $questions[] = $row;
                    } else {
                        $answers = array();
                        $answers['type'] = 'open';
                        $row['answers'] = $answers;
                        $questions[] = $row;
                    }
                } 
            }
        }
        return $questions;
    }

    /**
     * getAttendeeTypes
     *
     * @param  mixed $event_id
     * @param  mixed $lang_id
     * @return void
     */
    static public function getAttendeeTypes($event_id, $lang_id)
    {
        return \App\Models\EventAttendeeType::where('event_id', '=', $event_id)->where('languages_id', '=', $lang_id)->where('status', 1)->get()->toArray();
    }

	public function updateFrontEventAttendeeProfile($formInput, $id)
	{
		$parts = explode(".", request()->hasFile('file')->path);

		try {

			$attendeeObj = json_decode($formInput['attendeeObj'], true);

			$infoObj = json_decode($formInput['infoObj'], true);

			$settings = json_decode($formInput['settings'], true);

			unset($attendeeObj['file'], $attendeeObj['image'], $attendeeObj['att_cv']);

			$attendeeObj['is_updated'] = 1;

			\App\Models\Attendee::where('id', $id)->update($attendeeObj);

			foreach ($infoObj as $key => $value) {

				$infoFeild =	\App\Models\AttendeeInfo::where('attendee_id', $id)->where('languages_id', $formInput['event']['language_id'])->where('name', $key)->first();

				if ($infoFeild) {

					$infoFeild->update(['value' => $value]);

				} else {

					\App\Models\AttendeeInfo::create([
						'attendee_id' => $id,
						'languages_id' => $formInput['event']['language_id'],
						'name' => $key,
						'value' => $value,
					]);

				}

			}

			// Profile image
			if(request()->hasFile('file')) {

				$file_name = 'image_' . time() . '.' . request()->file('file')->getClientOriginalExtension();

				request()->file('file')->move(config('cdn.cdn_upload_path') . '/assets/attendees/', $file_name);

				\App\Models\Attendee::where('id', $id)->update(['image' => $file_name]);

			}
			
			// Resume file
			if(request()->hasFile('attendee_cv')) {

				$attendee_cv = 'document_' . time() . '.' . request()->file('attendee_cv')->getClientOriginalExtension();

				request()->file('attendee_cv')->move(config('cdn.cdn_upload_path') . '/assets/attendees/cv/', $attendee_cv);

				\App\Models\Attendee::where('id', $id)->update(['attendee_cv' => $attendee_cv]);

			}
			
			return [
				"status" => 1,
				"message" => "Data Successfully Updated...."
			];

		} catch (\Exception $e) {

			return [
				"status" => 0,
				"message" => "Something went wrong"
			];

		}
	}

    static public function setAttendeeFieldSorting($eventId){
        $date = \Carbon\Carbon::now();
        $keys = array('phone','email','delegate_number','network_group','department','table_number','pa_street','pa_house_no','pa_post_code','pa_city','pa_country', 'show_industry','passport_no','date_of_issue_passport','date_of_expiry_passport','bio_info','age','gender','organization','about','show_custom_field','show_job_tasks','interest', 'place_of_birth','type');
        $attendee_fields = EventAttendeeFieldDisplaySorting::where('event_id',$eventId)->orderBy('sort_order')->get()->count();
        if ($attendee_fields == 0) {
            $attendee_setting = AttendeeSetting::where('event_id', $eventId)->get()->first();
            if($attendee_setting) {
                $attendee_setting = json_decode(json_encode($attendee_setting), true);
                $attendee_setting = array_filter($attendee_setting, function ($val) {
                    return ($val != 0);
                });
                $records = array();
                foreach ($attendee_setting as $key => $setting) {
                    if (in_array($key, $keys)) {
                        $tempArray = array();
                        $tempArray['event_id'] = $eventId;
                        $tempArray['fields_name'] = $key;
                        $tempArray['sort_order'] = array_search($key, $keys);
                        $tempArray['created_at'] = $date;
                        $tempArray['updated_at'] = $date;
                        $records[] = $tempArray;
                    }
                }
                EventAttendeeFieldDisplaySorting::insert($records);
            }
        }
    }

    static public function setSpeakerFieldSorting($eventId)
    {
        $date = \Carbon\Carbon::now();
        $keys = array('phone', 'email', 'delegate_number', 'network_group', 'department', 'table_number', 'pa_street', 'pa_house_no', 'pa_post_code', 'pa_city', 'pa_country', 'show_industry', 'passport_no', 'date_of_issue_passport', 'date_of_expiry_passport', 'bio_info', 'age', 'gender', 'organization', 'about', 'show_custom_field', 'show_job_tasks', 'interest', 'place_of_birth', 'type');
        $attendee_fields = EventSpeakerFieldDisplaySorting::where('event_id', $eventId)->orderBy('sort_order')->get()->count();
        if ($attendee_fields == 0) {
            $speaker_setting = SpeakerSetting::where('event_id', $eventId)->get()->first();
            if ($speaker_setting) {
                $speaker_setting = json_decode(json_encode($speaker_setting), true);
                $speaker_setting = array_filter($speaker_setting, function ($val) {
                    return ($val != 0);
                });
                $records = array();
                foreach ($speaker_setting as $key => $setting) {
                    if (in_array($key, $keys)) {
                        $tempArray = array();
                        $tempArray['event_id'] = $eventId;
                        $tempArray['fields_name'] = $key;
                        $tempArray['sort_order'] = array_search($key, $keys);
                        $tempArray['created_at'] = $date;
                        $tempArray['updated_at'] = $date;
                        $records[] = $tempArray;
                    }
                }
                EventSpeakerFieldDisplaySorting::insert($records);
            }
        }
    }
	
	/**
	 * getOrderAttendeeDetail
	 *
	 * @param  mixed $event_id
	 * @param  mixed $order_id
	 * @param  mixed $attendee_id
	 * @return void
	 */
	function getOrderAttendeeDetail($event_id, $order_id, $attendee_id, $custom_fields = array())
	{
		$attendeeData = array();

		$order = new \App\Eventbuizz\EBObject\EBOrder([], $order_id);

		foreach ($order->getAttendees() as $order_attendee) {
			if($order_attendee->getModel()->id == $attendee_id) {

				$attendee = $order_attendee->getModel()->toArray();

				$order_attendee_model = $order_attendee->getOrderAttendee();

				$info = readArrayKey($attendee, [], 'info');
				
				$attendeeData['first_name'] = $attendee['first_name'];
				$attendeeData['last_name'] = $attendee['last_name'];
				$attendeeData['email'] = $attendee['email'];
				$attendeeData['confirm_email'] = $order_attendee_model->status === "complete" ? $attendee['email'] : '';
				$attendeeData['FIRST_NAME_PASSPORT'] = $attendee['FIRST_NAME_PASSPORT'];
				$attendeeData['LAST_NAME_PASSPORT'] = $attendee['LAST_NAME_PASSPORT'];
				$attendeeData['BIRTHDAY_YEAR'] = \Carbon\Carbon::parse($attendee['BIRTHDAY_YEAR'])->format('Y-m-d');
				$attendeeData['EMPLOYMENT_DATE'] = $attendee['EMPLOYMENT_DATE'];
				$attendeeData['title'] = $info['title'];
				$attendeeData['initial'] = $info['initial'];
				$attendeeData['company_name'] = $info['company_name'];
				$attendeeData['department'] = $info['department'];
				$attendeeData['delegate'] = $info['delegate_number'];
				$attendeeData['table_number'] = $info['table_number'];
				$attendeeData['network_group'] = $info['network_group'];
				$attendeeData['gender'] = $info['gender'];
				$attendeeData['organization'] = $info['organization'];
				$attendeeData['jobs'] = $info['jobs'];
				$attendeeData['interests'] = $info['interests'];
				$attendeeData['industry'] = $info['industry'];
				$attendeeData['about'] = $info['about'];
				$phone = $info['phone'];
				$phone_number = explode('-',$phone);
				$attendeeData['calling_code_phone'] = "+".ltrim($phone_number[0],"+");
				$attendeeData['phone'] = $phone_number[1];
				$attendeeData['country'] = $info['country'];
				$attendeeData['place_of_birth'] = $info['place_of_birth'];
				$attendeeData['passport_no'] = $info['passport_no'];
				$attendeeData['date_of_issue_passport'] = $info['date_of_issue_passport'];
				$attendeeData['date_of_expiry_passport'] = $info['date_of_expiry_passport'];
				$attendeeData['private_house_number'] = $info['private_house_number'];
				$attendeeData['private_street'] = $info['private_street'];
				$attendeeData['private_street_2'] = $info['private_street_2'];
				$attendeeData['private_state'] = $info['private_state'];
				$attendeeData['private_post_code'] = $info['private_post_code'];
				$attendeeData['private_city'] = $info['private_city'];
				$attendeeData['private_country'] = $info['private_country'];
				$attendeeData['age'] = $info['age'];
				
				//Event attendee type
				$attendeeData['attendee_type'] = $order_attendee->getAttendeeType();

				//News subscribers
				$attendeeData['subscriber_ids'] = (array) $order_attendee_model->subscriber_ids;

				//Food alergies | Accept GDPR | TOS | Membership
				$attendeeData['accept_foods_allergies'] = (int) $order_attendee_model->accept_foods_allergies;
				$attendeeData['accept_gdpr'] = (int) $order_attendee_model->accept_gdpr;
				$attendeeData['cbkterms'] = (int) $order_attendee_model->cbkterms;
				$attendeeData['member_number'] = (int) $order_attendee_model->member_number;

				//Custom fields
				$fields = $info['custom_field_id'.$event_id];
				foreach($custom_fields as $custom_field) {
					foreach($custom_field['children_recursive'] as $child) {
						if($custom_field['allow_multiple']) {
							$records = \App\Models\EventCustomField::join('conf_event_custom_fields_info', 'conf_event_custom_fields_info.custom_field_id', '=', 'conf_event_custom_fields.id')
								->where('conf_event_custom_fields.parent_id', $custom_field['id'])
								->whereIn('conf_event_custom_fields.id', explode(",", $fields))
								->where('conf_event_custom_fields_info.name', 'name')
								->select('conf_event_custom_fields.id as value', 'conf_event_custom_fields_info.value as label')
								->groupBy("conf_event_custom_fields_info.value")->get();
							if(count($records) > 0) {
								$attendeeData['custom-field-'.$custom_field['id']] = $records;
							}
						} else {
							if(in_array($child['id'], explode(",", $fields))) {
								$attendeeData['custom-field-'.$custom_field['id']] = $child['id'];
							}
						}
					}
				}
				
				//Spoken language
				$attendeeData['SPOKEN_LANGUAGE'] = \App\Models\Country::whereIn('language_name', explode(",", $attendee['SPOKEN_LANGUAGE']))->select('language_name as value', 'language_name as label')->groupBy("language_name")->get();
			
			}
		}

		return $attendeeData;
	}

	/**
	 * getOrderAttendeeBilling
	 *
	 * @param  mixed $event_id
	 * @param  mixed $order_id
	 * @param  mixed $attendee_id
	 * @return void
	 */
	public function getOrderAttendeeBilling($event_id, $order_id, $attendee_id)
	{
		$billing = \App\Models\AttendeeBilling::where('event_id', $event_id)->where('order_id', $order_id)->where('attendee_id', $attendee_id)->select('billing_company_type as company_type', 'billing_membership as member', 'billing_member_number as member_number','billing_private_street as private_street', 'billing_private_house_number as private_house_number', 'billing_private_post_code as private_post_code', 'billing_private_city as private_city', 'billing_private_country as private_country', 'billing_company_type as company_type', 'billing_company_registration_number as company_registration_number', 'billing_ean as ean', 'billing_contact_person_name as contact_person_name', 'billing_contact_person_email as contact_person_email', 'billing_contact_person_mobile_number as contact_person_mobile_number', 'billing_company_street as company_street', 'billing_company_house_number as company_house_number', 'billing_company_post_code as company_post_code', 'billing_company_city as company_city', 'billing_company_country as company_country', 'billing_poNumber as poNumber', 'invoice_reference_no as invoice_reference_no', 'billing_company_invoice_payer_company_name as company_invoice_payer_company_name', 'billing_company_invoice_payer_street_house_number as company_invoice_payer_street_house_number', 'billing_company_invoice_payer_post_code as company_invoice_payer_post_code', 'billing_company_invoice_payer_city as company_invoice_payer_city', 'billing_company_invoice_payer_country as company_invoice_payer_country', 'billing_company_street_2 as company_street_2', 'billing_company_state as company_state', 'billing_bruger_id as bruger_id')->first();

		$phone = $billing['contact_person_mobile_number'];
		$phone_number = explode('-',$phone);
		if(isset($phone_number[0]) && $phone_number[0]) {
			$billing['calling_code_contact_person_mobile_number'] = "+".ltrim($phone_number[0],"+");
		} else {
			$event_calling_code = EventRepository::getEventCallingCode(['event_id' => $event_id]);
			$calling_code = $event_calling_code ? $event_calling_code : '+45';
			$billing['calling_code_contact_person_mobile_number'] = $calling_code;
		}
		
		$billing['contact_person_mobile_number'] = $phone_number[1];

		//Confirm email same as email
		$billing['contact_person_confirm_email'] = $billing['contact_person_email'];

		return $billing;
	}

	/**
	 * Auto registration
	 *
	 * @param array
	 */
	public function autoregister($formInput)
	{
		$labels = $formInput['event']['labels'];
		
		$attendee_exists = false;

		$ids_str = base64_decode($formInput['ids']);

        $ids_arr = explode('-',$ids_str);

        $event_id = $ids_arr[0];

        $attendee_invite_id = $ids_arr[1];

        $event = \App\Models\Event::find($event_id);

        $event_organizer_id = $event->organizer_id;

        $language_id = $event->language_id;

		if($formInput['event']['url'] != $event->url )
        {
            return array(
				'success' => false,
				'message' => 'Something went wrong.',
			);
        }

		$payment_settings = EventSiteSettingRepository::getPaymentSetting($formInput);

		$eventsite_settings = EventSiteSettingRepository::getSetting($formInput);

		$inviteModel = \App\Models\AttendeeInvite::find($attendee_invite_id);

		$registration_form = EventSiteSettingRepository::getRegistrationFormById(['event_id' =>  $event_id, 'id' => $inviteModel->registration_form_id]);

		$attendee = \App\Models\Attendee::where('email', $inviteModel->email)->where('organizer_id', $event_organizer_id)->first();

		$payment_form_setting = EventSiteSettingRepository::getPaymentSetting(['registration_form_id' => $registration_form->id, "event_id" => $event_id]);

		$reg_items_count = \App\Models\BillingItem::where('event_id', $event_id)->where('registration_form_id', $registration_form->id)->where('type', 'item')->where('is_archive', '0')->whereNull('deleted_at')->where('status','1')->count();
		
		if($eventsite_settings['payment_type'] == 0 && $payment_form_setting->show_subregistration == 0 && $reg_items_count == 0 && $eventsite_settings['quick_register'] == 1)
        {
            if(!($inviteModel instanceof \App\Models\AttendeeInvite))
            {
                return array(
					'success' => false,
					'message' => 'Register link expired',
				);
            }


            if($attendee instanceof \App\Models\Attendee)
            {
                $event_attendee = \App\Models\EventAttendee::where('attendee_id', $attendee->id)->where('event_id', $event_id)->whereNull('deleted_at')->first();
                if($event_attendee instanceof \App\Models\EventAttendee)
                {
                    return array(
						'success' => false,
						'message' => 'Attendee already registered',
						'registration_form' => $registration_form
					);
                }
                else
                {
                    $attendee_exists = true;
                }
            }

			//Attendee [Create|Update]
            if($attendee_exists)
            {
                $attendee = $attendee;
                $attendee->status = 1;
                $attendee->save();
            } else {
                $attendee =  new \App\Models\Attendee();
                $attendee->email = $inviteModel->email;
                $attendee->first_name = $inviteModel->first_name;
                $attendee->last_name = $inviteModel->last_name;
                $attendee->phone = $inviteModel->phone;
                $attendee->organizer_id = $inviteModel->organizer_id;
                $attendee->status = 1;
                $attendee->save();
            }

            $info_fields = array('delegate_number','table_number','age','gender','company_name','company_key','title','industry','about','phone','registration_type','country','organization','jobs','interests','allow_vote','allow_gallery','initial','department','custom_field_id','network_group');

            foreach($info_fields as $field)
            {
                $info = new \App\Models\AttendeeInfo();
                $info->name = $field;
                $info->value = '';
                $info->languages_id = $language_id;
                $info->attendee_id = $attendee->id;
                $info->status = 1;
                $match_array = array_diff_key($info->toArray(), array_flip(['status','value']));
                $values_array = $info->toArray();
                \App\Models\AttendeeInfo::firstOrCopyOrCreateEmpty($match_array, $values_array);
            }

            $order_number = $payment_settings->eventsite_invoice_currentnumber; //use the current number for our order
            $payment_settings->eventsite_invoice_currentnumber = $order_number + 1; //update by incrementing 1
            $payment_settings->save(); 

			//Validate waiting list order
			$active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>   $event_id, 'status' => ['draft', 'completed']], false, true);

			//Validate form stock
			$total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $registration_form->id], true);
            
			$total = $total + 1;
			
			//Validate global stock
			$global_total = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => 0], true);

			$global_total = $global_total + 1;

			$waiting_list_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => request()->event_id, 'registration_form_id' => $registration_form->id]);

			if($waiting_list_setting->status == 1 || ($waiting_list_setting->after_stocked_to_waitinglist == 1 && (((int)$eventsite_form_setting->ticket_left > 0 && $total > (int)$eventsite_form_setting->ticket_left) || ((int)$eventsite_setting->ticket_left > 0 && $global_total > (int)$eventsite_setting->ticket_left)))) {
				
				$is_waitinglist = 1;

			} else {

				$is_waitinglist = 0;

				//Assign Event
				$event_attendee =  new \App\Models\EventAttendee();
				$event_attendee->event_id = $event_id;
				$event_attendee->attendee_id = $attendee->id;
				$event_attendee->save();
			}

			//Create order
            $order = new \App\Models\BillingOrder();
            $order->attendee_id = $attendee->id;
            $order->event_id = $event_id;
            $order->language_id = $language_id;
            $order->grand_total = 0;
            $order->summary_sub_total = 0;
            $order->total_attendee = 1;
            $order->is_new_flow = 1;
            $order->code = '';
            $order->coupon_id = '';
            $order->status = 'completed';
            $order->is_free = 1;
            $order->is_archive = 0;
            $order->is_waitinglist = $is_waitinglist;
            $order->reporting_panel_total = 0;
            $order->corrected_total = 0;
            $order->order_number = $order_number;
            $order->order_date = \Carbon\Carbon::now();
            $order->is_payment_received = 1;
            $order->payment_received_date = \Carbon\Carbon::now();
            $order->eventsite_currency = $payment_settings->eventsite_currency;
            $order->session_data = serialize(\Session::all());
            $order->user_agent = serialize($_SERVER);
            $order->save();

            //Assign Attendee to Order
            $order_attendee = new \App\Models\BillingOrderAttendee();
            $order_attendee->order_id = $order->id;
            $order_attendee->attendee_id = $order->attendee_id;
            $order_attendee->attendee_type = $registration_form->type_id;
            $order_attendee->save();

            $order = new \App\Eventbuizz\EBObject\EBOrder([], $order->id);
			
            $this->eventsiteBillingOrderRepository->registerConfirmationEmail($order);

            return array(
				'registered' => 1,
				'order' => $order->getModel(),
				'success' => true,
				'registration_form' => $registration_form
			);

        } else {
			if($eventsite_settings['prefill_reg_form'] == 0)
            {
                return array(
					'success' => true,
					'message' => $labels['REGISTRATION_FORM_AUTO_PREFILL_DISABLED'],
					'registration_form' => $registration_form
				);
            }

			if($attendee instanceof \App\Models\Attendee)
            {
                $info = \App\Models\AttendeeInfo::where('attendee_id', $attendee->id)->where('languages_id', $language_id)->whereNull('deleted_at')->get()->toArray();

                $info_fields = array('delegate_number','table_number','age','gender','company_name','company_key','title','industry','about','phone','registration_type','country','organization','jobs','interests','allow_vote','allow_gallery','initial','department','custom_field_id','network_group', 'place_of_birth', 'passport_no', 'date_of_issue_passport', 'date_of_expiry_passport', 'private_house_number', 'private_street', 'private_street_2', 'private_state', 'private_post_code', 'private_city', 'private_country');

                foreach($info_fields as $field_name)
                {
                    foreach($info as $data)
                    {
                        if(strtolower($data['name']) == $field_name)
                        {
                            if($field_name == 'delegate_number')
                            {
                                $infoFields['delegate'] = $data['value'];
                            }
                            else if($field_name == 'phone')
                            {
								$phone = $data['value'];
								$phone_number = explode('-',$phone);
								$infoFields['calling_code_phone'] = "+".ltrim($phone_number[0],"+");
                                $infoFields[$field_name] = $phone_number[1];
                            }
                            else if($field_name == 'country')
                            {
                                $infoFields['company_country'] = $data['value'];
                            }
                            else
                            {
                                $infoFields[$field_name] = $data['value'];
                            }
                        }
                    }
                }

                $attendee_data = $attendee->toArray();

                $attendeee_data = ['email', 'first_name', 'last_name', 'SPOKEN_LANGUAGE', 'EMPLOYMENT_DATE', 'BIRTHDAY_YEAR', 'LAST_NAME_PASSPORT', 'FIRST_NAME_PASSPORT'];

                $attendeee_data = array_intersect_key($attendee_data, array_flip($attendeee_data));

				if(is_array($infoFields) && count($infoFields) > 0) {
					$attendeee_data = array_merge($attendeee_data, $infoFields);
				}
                
                if($attendeee_data['SPOKEN_LANGUAGE'] != '')
                {
                    $attendeee_data['SPOKEN_LANGUAGE'] = \App\Models\Country::whereIn('language_name', explode(',', $attendeee_data['SPOKEN_LANGUAGE']))->select('language_name as value', 'language_name as label')->groupBy("language_name")->get();;
                }

				$attendeee_data['confirm_email'] = $attendee_data['email'];

                return array(
					'success' => true,
					'attendee' => $attendeee_data,
					'registration_form' => $registration_form
				);

            }

			return array(
				'success' => false,
				'message' => $labels['REGISTRATION_FORM_AUTO_REGISTERED_LINK_EXPIRED'],
				'registration_form' => $registration_form
			);
		}
	}
	
	/**
	 * getGroups
	 *
	 * @param  mixed $link_to_id
	 * @param  mixed $event_id
	 * @param  mixed $language_id
	 * @return void
	*/
	public static function getGroups($link_to_id, $event_id, $language_id) {
		$attendee_groups = \App\Models\EventGroup::where('event_id', $event_id)->whereIn('id', explode(',', $link_to_id))
			->with(['info' => function ($query) use ($language_id){
				return $query->where('languages_id', $language_id);
			}])
			->with(['parent' => function ($r) {
				return $r->orderBy('sort_order', 'asc')->orderBy('id','asc');
			}, 'parentInfo' => function ($r) use($language_id){
				return $r->where('languages_id', $language_id);
			}])
			->orderBy('sort_order','asc')
			->orderBy('id','asc')
			->get();
		return $attendee_groups;
	}
	
	/**
	 * attachAttendeeGroups
	 *
	 * @param  mixed $groups
	 * @param  mixed $event_id
	 * @param  mixed $attendee_id
	 * @param  mixed $linked_from
	 * @return void
	 */
	public static function attachAttendeeGroups($groups, $event_id, $attendee_id, $linked_from = '') {
		$assigned = [];
		foreach ($groups as $group) {
			if ($group['parent']['allow_multiple'] == 1) {
				if (!in_array($group['parent_id'], $assigned)) {
					$group_ids = \App\Models\EventGroup::where('event_id', $event_id)->where('parent_id', $group['parent_id'])->select('id')->get()->toArray();
					$result = \App\Models\EventAttendeeGroup::where('attendee_id', $attendee_id)->whereIn('group_id', $group_ids)->get();
					if (count($result) > 0) {
						$assigned[] = $group['parent_id'];
					} else {
						$assigned[] = $group['parent_id'];
						$values_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id);
						$match_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id);
						\App\Models\EventAttendeeGroup::updateOrCreate($match_array, $values_array);
					}
				}
			} else {
				$values_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id);
				if(!empty($linked_from)){
					$values_array['linked_from'] = $linked_from;
				}
				$match_array = array('group_id' => $group['id'], 'attendee_id' => $attendee_id);
				\App\Models\EventAttendeeGroup::updateOrCreate($match_array, $values_array);
			}
		}
		return true;
	}

	/**
	 * getCustomFieldsAnswers
	 *
	 * @param  mixed $attendee_info
	 * @param  mixed $formInput
	 * @return void
	 */
	public static function getCustomFieldsAnswers($attendee_info, $formInput) {
		$attendee_info = readArrayKey($attendee_info, $response, 'info');
		$custom_fields = \App\Eventbuizz\Repositories\EventSiteRepository::getCustomFields(['event_id' => $formInput['event_id'], 'language_id' => $formInput['language_id'], 'registration_form_id' => $formInput['registration_form_id']]);
		$fields = $attendee_info['custom_field_id'.$formInput['event_id']];
		foreach($custom_fields as $key => $custom_field) {
			foreach($custom_field['children_recursive'] as $child) {
				if($custom_field['allow_multiple']) {
					$records = \App\Models\EventCustomField::join('conf_event_custom_fields_info', 'conf_event_custom_fields_info.custom_field_id', '=', 'conf_event_custom_fields.id')
						->where('conf_event_custom_fields.parent_id', $custom_field['id'])
						->whereIn('conf_event_custom_fields.id', explode(",", $fields))
						->where('conf_event_custom_fields_info.name', 'name')
						->select('conf_event_custom_fields.id as value', 'conf_event_custom_fields_info.value as label')
						->groupBy("conf_event_custom_fields_info.value")->get();
					if(count($records) > 0) {
						$custom_fields[$key]['answers'] = $records->toArray();
					}
				} else {
					if(in_array($child['id'], explode(",", $fields))) {
						$custom_fields[$key]['answers'][0] = array(
							'value' => $child['id'],
							'label' => $child['name']
						);
					}
				}
			}
		}
		return $custom_fields;
	}

	public function attendeeNotAttending($formInput){

		$event_id = $formInput['event_id'];

		$event = \App\Models\Event::find($event_id);

		$labels = $formInput['event']['labels'];

		$organizer_id = $formInput['organizer_id'];

		$attendee_invite = \App\Models\AttendeeInvite::where('id', '=', $formInput['id'])
							->where('event_id', '=', $formInput['event_id'])
							->first();

		if($formInput['confirm'] == 1) {

			if(!$attendee_invite || $attendee_invite->is_attending === 1) {
				return [
					'success' => false,
					'data'=> [
						'already_done' => 1,
						"redirect" => "home_page",
						"message" => "already consumed"
					]
				];
			}

			$att_id = $formInput['id'];

			if(isset($formInput['email'])) {
				$attendees = \App\Models\Attendee::where('email', '=', $formInput['email'])
							->where('organizer_id', '=', $organizer_id)
							->whereNull('deleted_at')
							->get()->toArray();
				$att_id = $attendees[0]['id'];
			}

			$order_data = \App\Models\BillingOrder::where('event_id',$formInput['event_id'])
						->where('attendee_id',$att_id)
						->where('is_archive','0')
						->where('status','<>','cancelled')
						->currentOrder()->first();
			
			if($order_data instanceof \App\Models\BillingOrder)
			{
				return [
					'success' => false,
					'data'=> [
						'already_done' => 0,
						"redirect" => "home_page",
						"message" => $labels['REGISTRATION_GENERAL_NOT_ALLOWED_CANCELLATION_REGISTRATION_ALERT']
					]
				];
			}

			$already_register = \App\Models\EventAttendee::where('attendee_id', '=', $att_id)
								->where('event_id', '=', $formInput['event_id'])
								->whereNull('deleted_at')
								->get();

			$support_email = \App\Models\EventInfo::where('event_id', '=', $event_id)
							->where('languages_id', '=', $event->language_id)
							->where('name', '=', 'support_email')
							->first();
			
			if($attendee_invite) {
				$attendee_invite->is_attending = 1;
				$attendee_invite->save();
				$subject = 'Event id: '.$event_id.' - "'.$attendee_invite->first_name.' '.$attendee_invite->last_name.'" is not coming - Please check the not attendee list in EventCenter';
			}


			if ($already_register) {
				$attendee = \App\Models\Attendee::where('organizer_id', '=', $organizer_id)->first();
				if(isset($formInput['email'])){
					$attendee =  $attendee->where('email', '=', $formInput['email']);
				}else{
					$attendee = $attendee->where('id', '=', $formInput['id']);
				}
				$attendee = $attendee->first();
				$subject = 'Event id: '.$event_id.' - "'.$attendee->first_name.' '.$attendee->last_name.'" is not coming - Please check the not attendee list in EventCenter';
			}

            $attendee_setting = \App\Models\AttendeeSetting::where('event_id', '=', $event_id)->first();

			if($support_email->value && $attendee_setting->send_email_to_organizer == 1) {
				$data['email'] = $support_email->value;
				$data['from_name'] = 'Eventbuizz';
				$data['organizer_id'] = $formInput['organizer_id'];
				$data['event_id'] = $formInput['id'];
				$data['subject'] = $subject;
				$data['content'] = '';
				$data['view'] = 'email.plain-text';
				\Mail::to($support_email->value)->send(new Email($data));
			}

			return [
				'success' => true,
				'data'=> [
					'already_done' => 0,
					"redirect" => "home_page",
					"message" => "cancellation sucessfull"
				]
			];

		}

		return [
			'success' => false,
			'data'=> [
				'already_done' => 0,
				"redirect" => "home_page",
				"message" => "No change happened"
			]
		];
	}
	
	/**
	 * autoFillAttendeeFields
	 *
	 * @param  mixed $sections
	 * @param  mixed $attendee
	 * @return void
	 */
	public static function autoFillAttendeeFields($sections, $attendee)
	{
		if (count($sections)) {
			foreach ($sections as $section) {
				if(in_array($section['field_alias'], ['basic', 'address_private', 'membership'])) {
					foreach ($section['fields'] as $field) {
						if($field['is_autofill'] == 0) {
							if(!in_array($field['field_alias'], ['attendee_type'])) {
								if($field['field_alias'] == 'custom_field_id') {
									$prefix = 'custom-field-';
									foreach ($attendee as $key => $value) {
										if (strpos($key, $prefix) === 0) {
											unset($attendee[$key]);
										}
									}
								} else {
									$attendee[$field['field_alias']] = '';
								}
							}
						}
					}
				}
			}
		}
		return $attendee;
	}

	/**
	 * validateAttendee
	 *
	 * @param  mixed $formInput
	 * @param  mixed $attendee_id
	 * @param  mixed $verification_id
	 * @return void
	 */
	public function validateAttendee($formInput, $attendee_id, $verification_id)
	{
		$event = $formInput['event'];
		
		$labels = $formInput['event']['labels'];
		
		$event_attendee = \App\Models\EventAttendee::where('verification_id', $verification_id)->first();
		
		$attendee_order = \App\Models\BillingOrder::where('event_id', $event['id'])->where('attendee_id', $attendee_id)->where('is_archive','0')->where('status','completed')->currentOrder()->with('order_attendees')->first();
	  
		if( ! $attendee_order instanceof \App\Models\BillingOrder)
        {
			return array(
				"success" => true,
				"message" => "Your order doesn't exits"
			);
        }

		$attendee_data_assing = array('event_id' => $event['id'], 'attendee_id' => $attendee_id, 'default_language_id' => $event['language_id'], 'is_active' => '1');
        
		$match_array = array('event_id' => $event['id'], 'attendee_id' => $attendee_id, 'default_language_id' => $event['language_id']);
        
		\App\Models\EventAttendee::updateOrCreate($match_array,$attendee_data_assing);
		
		$order_attendees = $attendee_order->order_attendees;
		
        foreach($order_attendees as $att)
        {
            $attendee_data_assing = [];

            $match_array = [];

            if($att->attendee_id == $attendee_id) { continue; } 

            $attendee_data_assing = array('event_id' => $event['id'], 'attendee_id' => $att->attendee_id, 'default_language_id' => $event['language_id'], 'is_active' => '1');

            $match_array = array('event_id' => $event['id'], 'attendee_id' => $att->attendee_id, 'default_language_id' => $event['language_id']);

            \App\Models\EventAttendee::updateOrCreate($match_array, $attendee_data_assing);
        }

		// Validate Attendee

		if($event_attendee) {

			if($event_attendee->is_active == 0) {
	
				$event_attendee->is_active = 1;
	
				$event_attendee->save();
	
				$order = new \App\Eventbuizz\EBObject\EBOrder([], $attendee_order->id);
	
				$this->eventsiteBillingOrderRepository->registerConfirmationEmail($order);
	
				return array(
					"success" => true,
					"message" => $labels['REGISTRATION_ATTENDEE_CONFIRMATION']
				);
	
			} else {
				return array(
					"success" => false,
					"message" => $labels['REGISTRATION_ATTENDEE_ALREADY_CONFIRM']
				);
			}

		} else {

			return array(
				"success" => false,
				"message" => 'Email does not exist'
			);
			
		}
	}

	public static function validateAttendeeRegistration($attendee_setting, $labels, $organizer_id, $event_id, $email, $validate_index = null)
	{

		$attendee_invite = \App\Models\AttendeeInvite::where('organizer_id', $organizer_id)->where('event_id', $event_id)->where('email', $email)->first();

		$domain_data = explode("@", $email);

		$all_domains = array();

		if (trim($attendee_setting['domain_names'])) {
			$domain_names = explode(',', $attendee_setting['domain_names']);
			foreach ($domain_names as $allDomains) {
				$all_domains[trim(strtolower($allDomains))] = trim(strtolower($allDomains));
			}
		}
		
		if($attendee_setting["validate_attendee_invite_with_domain"] == 0) {

			// Attendee invite validation
			if ($attendee_invite) {
				if ($attendee_invite->is_attending == 1) {
					return [
						'success' => false,
						'label' => $labels['REGISTRATION_INVITE_NOT_ATTENDING_ERROR'],
						'validate_index' => $validate_index
					];
				}
			} else {
				if ($attendee_setting["validate_attendee_invite"] == "1") {
					return [
						'success' => false,
						'label' => $labels['REGISTRATION_INVITE_VALIDATION_ERROR'],
						'validate_index' => $validate_index
					];
				}
			}
	
			// Domain validation
			if (count($all_domains) > 0) {
				if (!in_array(strtolower($domain_data[1]), $all_domains)) {
					return [
						'success' => false,
						'label' => $labels['REGISTER_VALID_DOMAIN'],
						'validate_index' => $validate_index
					];
				}
			}

			if($attendee_setting["validate_attendee_invite"] == "1" || count($all_domains) > 0) {
				if(trim($email) == '') {
					return [
						'success' => false,
						'label' => $labels['REGISTRATION_ERROR_EMAIL'],
						'validate_index' => $validate_index
					];
				}
			}

		} else {
			if((!$attendee_invite || ($attendee_invite && $attendee_invite->is_attending == 1)) && (count($all_domains) == 0 || (count($all_domains) > 0 && !in_array(strtolower($domain_data[1]), $all_domains)))) {
				return [
					'success' => false,
					'label' => $labels['REGISTER_VALID_DOMAIN_AND_INVITE'],
					'validate_index' => $validate_index
				];
			}
		}

		return [
			'success' => true,
		];
	}
			
	/**
	 * refactorDataByAttendeeTypeSettings
	 *
	 * @param  mixed $event_id
	 * @param  mixed $attendee
	 * @param  mixed $type_id
	 * @param  mixed $gdpr
	 * @return void
	 */
	public static function refactorDataByAttendeeTypeSettings($event_id, $attendee, $type_id, $gdpr = true)
	{
		$attributes = ['email', 'ss_number', 'end_date', 'password', 'organizer_id', 'status', 'show_home', 'image', 'country_id', 'allow_vote', 'billing_password', 'billing_ref_attendee', 'SPOKEN_LANGUAGE', 'EMPLOYMENT_DATE', 'BIRTHDAY_YEAR', 'LAST_NAME_PASSPORT', 'FIRST_NAME_PASSPORT', 'change_password', 'phone', 'is_updated'];

		if(is_array($attendee)) {

			if(!$gdpr) {

				foreach($attributes as $key) {
					if(isset($attendee[$key])) {
						$attendee[$key] = '';
					}
				}

				$attendee['info'] = [];

				return $attendee;
			}

			$settings = self::getAttendeeTypeSettings($event_id, $type_id);
			$attendee['email'] = $settings['email'] === 1 ? $attendee['email'] : "";
			$attendee['phone'] = $settings['phone'] === 1 ? $attendee['phone'] : "";
			$attendee['FIRST_NAME_PASSPORT'] = $settings['first_name_passport'] === 1 ? $attendee['FIRST_NAME_PASSPORT'] : "";
			$attendee['LAST_NAME_PASSPORT'] = $settings['last_name_passport'] === 1 ? $attendee['LAST_NAME_PASSPORT'] : "";
			$attendee['BIRTHDAY_YEAR'] = $settings['birth_date'] === 1 ? $attendee['BIRTHDAY_YEAR'] : "";
			$attendee['SPOKEN_LANGUAGE'] = $settings['spoken_languages'] === 1 ? $attendee['SPOKEN_LANGUAGE'] : "";
			$attendee['image'] = $settings['profile_picture'] === 1 ? $attendee['image'] : "";
			$attendee['EMPLOYMENT_DATE'] = $settings['employment_date'] === 1 ? $attendee['EMPLOYMENT_DATE'] : "";
			$attendee['info'] = self::refactorAttendeeInfo($attendee['info'], $settings);
		} else {

			if(!$gdpr) {

				foreach($attributes as $key) {
					if(isset($attendee->{$key})) {
						$attendee->{$key} = '';
					}
				}

				$attendee->info = [];

				return $attendee;

			}

			$settings = self::getAttendeeTypeSettings($event_id, $type_id);
			$attendee->email = $settings['email'] === 1 ? $attendee->email : "";
			$attendee->phone = $settings['phone'] === 1 ? $attendee->phone : "";
			$attendee->FIRST_NAME_PASSPORT = $settings['first_name_passport'] === 1 ? $attendee->FIRST_NAME_PASSPORT : "";
			$attendee->LAST_NAME_PASSPORT = $settings['last_name_passport'] === 1 ? $attendee->LAST_NAME_PASSPORT : "";
			$attendee->BIRTHDAY_YEAR = $settings['birth_date'] === 1 ? $attendee->BIRTHDAY_YEAR : "";
			$attendee->SPOKEN_LANGUAGE = $settings['spoken_languages'] === 1 ? $attendee->SPOKEN_LANGUAGE : "";
			$attendee->image = $settings['profile_picture'] === 1 ? $attendee->image : "";
			$attendee->EMPLOYMENT_DATE = $settings['employment_date'] === 1 ? $attendee->EMPLOYMENT_DATE : "";
			$attendee->info = self::refactorAttendeeInfo($attendee->info, $settings);
		}
		
		return $attendee;
	}
		
	/**
	 * getAttendeeTypeSettings
	 *
	 * @param  mixed $event_id
	 * @param  mixed $type_id
	 * @param  mixed $detail
	 * @return void
	 */
	public static function getAttendeeTypeSettings($event_id, $type_id, $detail = false)
	{
		$array = array();

		$keys = array('initial', 'first_name', 'last_name', 'show_industry', 'email', 'organization', 'show_job_tasks', 'interest', 'age', 'gender', 'phone', 'password', 'department', 'network_group', 'delegate_number', 'table_number', 'first_name_passport', 'last_name_passport', 'place_of_birth', 'passport_no', 'date_of_issue_passport', 'date_of_expiry_passport', 'birth_date', 'employment_date', 'spoken_languages', 'profile_picture', 'resume', 'type', 'bio_info', 'show_custom_field', 'pa_street', 'pa_house_no', 'pa_post_code', 'pa_city', 'pa_country', 'country', 'company_name', 'title');

		$query = EventAttendeeFieldDisplaySorting::where('event_id', $event_id)->orderBy('sort_order');

		$settings = $query->where('event_attendee_type_id', $type_id)->get();

		if(count($settings) == 0) {
			$settings = EventAttendeeFieldDisplaySorting::where('event_id', $event_id)->orderBy('sort_order')->where('event_attendee_type_id', 0)->get();
		}

		if(count($settings) > 0) {

			$settings = $settings->toArray();

			foreach($keys as $key) {
				$setting = array_values(array_filter($settings, function($setting) use($key) {
					return $setting['fields_name'] == $key;
				}));
				if($detail) {
					$array[$key] = array(
						'status' => count($setting) > 0 ? 1 : 0,
						'is_editable' => count($setting) > 0 ? $setting[0]['is_editable'] : 0,
						'is_private' => count($setting) > 0 ? $setting[0]['is_private'] : 0
					);
				} else {
					$array[$key] = count(array_filter($settings, function($setting) use($key) {
						return $setting['fields_name'] == $key;
					})) > 0 ? 1 : 0;
				}
			}

		} else {
			foreach($keys as $key) {
				if($detail) {
					$array[$key] = array(
						'status' => 0,
						'is_editable' => 0,
						'is_private' => 0
					);
				} else {
					$array[$key] = 0;
				}
			}
		}

		return $array;
	}
		
	/**
	 * getAttendeeTypeIDByRole
	 *
	 * @param  mixed $event_id
	 * @param  mixed $alias
	 * @param  mixed $lang_id
	 * @return void
	 */
	public static function getAttendeeTypeIDByRole($event_id, $alias, $lang_id)
	{
		$row = \App\Models\EventAttendeeType::where('event_id', $event_id)->where('alias', $alias)->where('languages_id', $lang_id)->first();

		if($row) {
			return $row->id;
		} else {
			return 0;
		}
	}
	
	/**
	 * getAttendeeFieldsByAttendeeType
	 *
	 * @param  mixed $attendee_type_id
	 * @param  mixed $event_id
	 * @param  mixed $language_id
	 * @return void
	 */
	public static function getAttendeeFieldsByAttendeeType($attendee_type_id, $event_id, $language_id)
    {
		$attendeeLabels = [];

		if($attendee_type_id > 0) {
			
			$registration_form = \App\Models\RegistrationForm::where('type_id', $attendee_type_id)->first();

			if($registration_form) {

				$fields = \App\Models\BillingField::where('registration_form_id', $registration_form->id)
					->where('type', 'field')
                    ->with(['info' => function($query) use ($language_id){
                        return $query->where('languages_id', '=', $language_id);
                    }])
                ->get();

				if(count($fields)) {
                    foreach ($fields as $field) {
                        foreach ($field->info as $info) {
                            if($info->name == 'name') {
                                $attendeeLabels[$field->field_alias] = $info->value;
                            }
                        }
                    }
                }

			}
		}

		if(count($attendeeLabels) == 0) {

			$labels = \App\Models\EventSiteText::where('event_id', $event_id)
                ->where('parent_id', '0')
                ->where('module_alias', 'attendees')
                ->with(['info' => function ($query) use ($language_id) {
                    return $query->where('languages_id', $language_id);
                }])
                ->with(['children' => function ($r) {
                    return $r->orderBy('constant_order');
                }, 'children.childrenInfo' => function ($rr) use ($language_id) {
                    return $rr->where('languages_id', $language_id);
                }])
                ->orderBy('section_order')->get()->toArray();

            foreach ($labels[0]['children'] as $row) {
                if (count($row['children_info']) > 0) {
                    foreach ($row['children_info'] as $val) {
                        $attendeeLabels[$row['alias']] = $val['value'];
                    }
                }
            }

			$labels = array();

			$settings = array(
				'fields' => array(
					'initial' => array(
						'field' => 'initial',
						'label' => 'Initial',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_INITIAL',
						'field_alias' => 'initial'
					),
					'first_name' => array(
						'field' => 'first_name',
						'label' => 'First Name',
						'type' => 'string',
						'required' => true,
						'alias' => 'ATTENDEE_FIRST_NAME',
						'field_alias' => 'first_name'
					),
					'last_name' => array(
						'field' => 'last_name',
						'label' => 'Last name',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_LAST_NAME',
						'field_alias' => 'last_name'
					),
					'show_industry' => array(
						'field' => 'show_industry',
						'label' => 'Industry',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_INDUSTRY',
						'field_alias' => 'industry'
					),
					'email' => array(
						'field' => 'email',
						'label' => 'Email',
						'type' => 'string',
						'required' => true,
						'alias' => 'ATTENDEE_EMAIL',
						'field_alias' => 'email'
					),
					'organization' => array(
						'field' => 'organization',
						'label' => 'Organization',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_ORGANIZATION',
						'field_alias' => 'organization'
					),
					'show_job_tasks' => array(
						'field' => 'show_job_tasks',
						'label' => 'Job Tasks',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_JOB_TASKS',
						'field_alias' => 'jobs'
					),
					'interest' => array(
						'field' => 'interest',
						'label' => 'Interests',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_INTERESTS',
						'field_alias' => 'interests'
					),
					'age' => array(
						'field' => 'age',
						'label' => 'Age',
						'type' => 'integer',
						'required' => false,
						'alias' => 'ATTENDEE_AGE',
						'field_alias' => 'age'
					),
					'gender' => array(
						'field' => 'gender',
						'label' => 'Gender',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_GENDER',
						'field_alias' => 'gender'
					),
					'phone' => array(
						'field' => 'phone',
						'label' => 'Phone',
						'type' => 'integer',
						'required' => false,
						'alias' => 'ATTENDEE_PHONE',
						'field_alias' => 'phone'
					),
					'password' => array(
						'field' => 'password',
						'label' => 'Password',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PASSWORD',
						'field_alias' => 'password'
					),
					'department' => array(
						'field' => 'department',
						'label' => 'Department',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_DEPARTMENT',
						'field_alias' => 'department'
					),
					'network_group' => array(
						'field' => 'network_group',
						'label' => 'Network Group',
						'type' => 'string',
						'required' => false,
						'alias' => 'GENERAL_NETWORK_GROUP',
						'field_alias' => 'network_group'
					),
					'delegate_number' => array(
						'field' => 'delegate_number',
						'label' => 'Delegate Number',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_DELEGATE_NUMBER',
						'field_alias' => 'delegate'
					),
					'table_number' => array(
						'field' => 'table_number',
						'label' => 'Table Number',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_TABLE_NUMBER',
						'field_alias' => 'table_number'
					),
					'first_name_passport' => array(
						'field' => 'first_name_passport',
						'label' => 'First name (Passport)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PASSPORT_FIRST_NAME',
						'field_alias' => 'FIRST_NAME_PASSPORT'
					),
					'last_name_passport' => array(
						'field' => 'last_name_passport',
						'label' => 'Last name (Passport)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PASSPORT_LAST_NAME',
						'field_alias' => 'LAST_NAME_PASSPORT'
					),
					'place_of_birth' => array(
						'field' => 'place_of_birth',
						'label' => 'Place of birth(Passport)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PLACE_OF_BIRTH',
						'field_alias' => 'place_of_birth'
					),
					'passport_no' => array(
						'field' => 'passport_no',
						'label' => 'Passport no',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PASSPORT_NO',
						'field_alias' => 'passport_no'
					),
					'date_of_issue_passport' => array(
						'field' => 'date_of_issue_passport',
						'label' => 'Date of issue(Passport)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PASSPORT_ISSUE_DATE',
						'field_alias' => 'date_of_issue_passport'
					),
					'date_of_expiry_passport' => array(
						'field' => 'date_of_expiry_passport',
						'label' => 'Date of expiry(Passport)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PASSPORT_EXPIRY_DATE',
						'field_alias' => 'date_of_expiry_passport'
					),
					'birth_date' => array(
						'field' => 'birth_date',
						'label' => 'Birth Date',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_BIRTH_DATE',
						'field_alias' => 'BIRTHDAY_YEAR'
					),
					'employment_date' => array(
						'field' => 'employment_date',
						'label' => 'Employment Date',
						'type' => 'string',
						'required' => false,
						'alias' => 'EMPLOYMENT_DATE',
						'field_alias' => 'EMPLOYMENT_DATE'
					),
					'spoken_languages' => array(
						'field' => 'spoken_languages',
						'label' => 'Spoken Languages',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_SPOKEN_LANGUAGES',
						'field_alias' => 'SPOKEN_LANGUAGE'
					),
					'profile_picture' => array(
						'field' => 'profile_picture',
						'label' => 'Profile picture',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PROFILE_PICTURE',
						'field_alias' => 'profile_picture'
					),
					'resume' => array(
						'field' => 'resume',
						'label' => 'Resume',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_RESUME',
						'field_alias' => 'resume'
					),
					'type' => array(
						'field' => 'type',
						'label' => 'Type',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_TYPE',
						'field_alias' => 'attendee_type'
					),
					'bio_info' => array(
						'field' => 'bio_info',
						'label' => 'About',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_ABOUT',
						'field_alias' => 'about'
					),
					'show_custom_field' => array(
						'field' => 'show_custom_field',
						'label' => 'Custom fields',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_CUSTOM_FIELDS',
						'field_alias' => 'custom_field_id'
					),
					'pa_street' => array(
						'field' => 'pa_street',
						'label' => 'Street (private address)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PRIVATE_STREET',
						'field_alias' => 'private_street'
					),
					'pa_house_no' => array(
						'field' => 'pa_house_no',
						'label' => 'House number (private address)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PRIVATE_HOUSE_NUMBER',
						'field_alias' => 'private_house_number'
					),
					'pa_post_code' => array(
						'field' => 'pa_post_code',
						'label' => 'Post code (private address)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PRIVATE_POST_CODE',
						'field_alias' => 'private_post_code'
					),
					'pa_city' => array(
						'field' => 'pa_city',
						'label' => 'City (private address)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PRIVATE_CITY',
						'field_alias' => 'private_city'
					),
					'pa_country' => array(
						'field' => 'pa_country',
						'label' => 'Country (private address)',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_PRIVATE_COUNTRY',
						'field_alias' => 'private_country'
					),
					'country' => array(
						'field' => 'country',
						'label' => 'Country',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_COUNTRY',
						'field_alias' => 'country'
					),
					'company_name' => array(
						'field' => 'company_name',
						'label' => 'Company name',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_COMPANY_NAME',
						'field_alias' => 'company_name'
					),
					'title' => array(
						'field' => 'title',
						'label' => 'Title',
						'type' => 'string',
						'required' => false,
						'alias' => 'ATTENDEE_TITLE',
						'field_alias' => 'title'
					),
				)
			);

			foreach ($settings['fields'] as $key => $field) {
				if(isset($attendeeLabels[$field['alias']])) {
					$labels[$field['field_alias']] = $attendeeLabels[$field['alias']];
				}
			}
	
			return $labels;

		} else {
			return $attendeeLabels;
		}
	}

	/**
     * getAttendeeRegFormId
     *
     * @param  mixed $id
     * @param  mixed $event_id
     * @return void
     */
    public static function getAttendeeRegFormId($id, $event_id)
    {
        $event_attendee = \App\Models\EventAttendee::where('attendee_id', $id)->where('event_id', $event_id)->with(['regForm'])->first();
        
        if(!$event_attendee){
            return null;
        }
        
        $event_attendee = $event_attendee->toArray();
        
        if(!isset($event_attendee['reg_form']['id'])){
            return null;
        }
        
        return $event_attendee['reg_form']['id'];
    }

	/**
	 * getAttendeeTypeSettingsWithSort
	 *
	 * @param  mixed $event_id
	 * @param  mixed $type_id
	 * @return void
	 */
	public static function getAttendeeTypeSettingsWithSort($event_id, $type_id)
	{
		$array = array();

		$query = EventAttendeeFieldDisplaySorting::where('event_id', $event_id)->orderBy('sort_order');

		$settings = $query->where('event_attendee_type_id', $type_id)->get();

		if(count($settings) == 0) {
			$settings = EventAttendeeFieldDisplaySorting::where('event_id', $event_id)->orderBy('sort_order')->where('event_attendee_type_id', 0)->get();
		}

		if(count($settings) > 0) {

			$settings = $settings->toArray();

			foreach($settings as $key => $setting) {
				
				$array[$key] = array(
					'name' =>  $setting['fields_name'],
					'is_editable' => $setting['is_editable'],
					'is_private' =>  $setting['is_private']
				);
				
			}

		} 

		return $array;
	}
}