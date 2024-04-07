<?php

namespace App\Eventbuizz\Repositories;

use App\Mail\Email;
use App\Models\EventCloneLog;
use App\Models\Organizer;
use App\Models\SubAdminEvent;
use Illuminate\Database\Eloquent\Builder;
use App\Models\EventSetting;
use App\Models\Theme;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;
use App\Eventbuizz\Repositories\EventsiteRepository;
use App\Eventbuizz\Repositories\MediaLibraryRepository;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\EventsiteBillingOrderRepository;
use App\Eventbuizz\Repositories\SocialMediaRepository;
use App\Eventbuizz\Repositories\EventSiteSettingRepository;
use App\Events\Wizard\Event\CloneEvent;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventRepository extends AbstractRepository
{
    protected $model;

    protected $eventsiteBillingItemRepository;

    protected $eventsiteRepository;

    protected $mediaLibraryRepository;

    public $infoFields = array('support_email', 'dateformat', 'location_address', 'location_name', 'sms_organizer_name', 'domain_name');

    public function __construct(Request $request, Event $model, EventsiteBillingItemRepository $eventsiteBillingItemRepository, EventsiteRepository $eventsiteRepository, MediaLibraryRepository $mediaLibraryRepository)
    {
        $this->request = $request;
        $this->model = $model;
        $this->eventsiteBillingItemRepository = $eventsiteBillingItemRepository;
        $this->eventsiteRepository = $eventsiteRepository;
        $this->mediaLibraryRepository = $mediaLibraryRepository;
    }

    /**
     * Event installation / Event cloning
     *
     * @param array
     */
    public function install($request)
    {
        //Save Setting
        $data['event_id'] = $request['to_event_id'];
        $data['created_at'] = \Carbon\Carbon::now();
        $data['updated_at'] = \Carbon\Carbon::now();

        //Event Settings
        $settings = \App\Models\EventSetting::where('event_id', $request['from_event_id'])->get();
        
        if (count($settings)) {
            foreach ($settings as $setting) {
                $setting_instance = \App\Models\EventSetting::where('event_id', $request['to_event_id'])->where('name', $setting->name)->first();
                if ($setting_instance) {
                    $setting_instance->value = $setting->value;
                    $setting_instance->save();
                } else {
                    $setting_instance = \App\Models\EventSetting::find($setting->id)->replicate();
                    $setting_instance->event_id = $request['to_event_id'];
                    $setting_instance->save();
                }
            }
        } else {
            $data['name'] = 'poll_setting';
            $data['value'] = '1';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'primary_color';
            $data['value'] = '#f28121';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'secondary_color';
            $data['value'] = '#69c7cf';
            \App\Models\EventSetting::create($data);

            $data['value'] = 1;
            $data['name'] = 'badgeName';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'badgeTitle';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'badgeCompany';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'badgeDept';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'badgeLogo';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'badgeEventName';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'badgeTableNumber';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'badgeDelegateNumber';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'program_view';
            $data['value'] = 'vertical';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'desktop_program_mode';
            $data['value'] = '0';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'projector_mode';
            $data['value'] = 'moderator_camera';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'desktop_program_screen_sidebar_program';
            $data['value'] = '0';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'desktop_program_screen_sidebar_gdpr';
            $data['value'] = '0';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'desktop_program_screen_sidebar_checkin';
            $data['value'] = '0';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'enable_vp';
            $data['value'] = '0';
            \App\Models\EventSetting::create($data);

            $data['name'] = 'streaming_service';
            $data['value'] = 'vonage';
            \App\Models\EventSetting::create($data);
        }

        $event_cart_type = \App\Models\EventCardType::where('event_id', $request['from_event_id'])->first();
        
        if ($event_cart_type) {
            $duplicate = $event_cart_type->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            // For eventsite payment settings
            $purchase_policy = '<div class="nodeLabel " role="presentation"><span class="editable nodeText"><span>I agree to our</span></span><span class="nodeLabelBox repTarget" role="treeitem"><span class="editGroup nodeAttr"><span class="editable nodeValue"> </span></span><span class="editable nodeText"><span>&quot;<a href="https://www.eventbuizz.com/home/da/betingelser/" target="_blank">Purchase Policy</a>&quot;</span></span><!--<span class="nodeTag"--></span></div>';

            \App\Models\EventCardType::create([
                "event_id" => $request['to_event_id'],
                "organizer_id" => $request['organizer_id'],
                "card_type" => '',
                "purchase_policy" => $purchase_policy,
                "created_at" => \Carbon\Carbon::now(),
                "updated_at" => \Carbon\Carbon::now(),
            ]);
        }

        $event_disclaimer_setting = \App\Models\EventDisclaimerSetting::where('event_id', $request['from_event_id'])->first();

        if ($event_disclaimer_setting) {
            $duplicate = $event_disclaimer_setting->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            \App\Models\EventDisclaimerSetting::create([
                "event_id" => $request['to_event_id'],
            ]);
        }

        $event_document_setting = \App\Models\MyDocumentSetting::where('event_id', $request['from_event_id'])->first();

        if ($event_document_setting) {
            $duplicate = $event_document_setting->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            \App\Models\MyDocumentSetting::create([
                "event_id" => $request['to_event_id'],
            ]);
        }

        $event_gdpr_setting = \App\Models\EventGdprSetting::where('event_id', $request['from_event_id'])->first();

        if ($event_gdpr_setting) {
            $duplicate = $event_gdpr_setting->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            \App\Models\EventGdprSetting::create([
                "event_id" => $request['to_event_id'],
            ]);
        }

        $event_gdpr = \App\Models\EventGdpr::where('event_id', $request['from_event_id'])->first();
        if ($event_gdpr) {
            $duplicate = $event_gdpr->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            \App\Models\EventGdpr::create([
                "event_id" => $request['to_event_id'],
                "subject" => 'General Data Protection Regulation (GDPR)',
                "inline_text" => 'I agree to the GDPR. {detail_link} Read more.{/detail_link}',
                "description" => 'If you do not accept these terms only your "First Name" and "Last Name" will appear in following modules:<ul><li>Attendee</li><li>Chat</li><li>Survey</li><li>Polls</li><li>Q&amp;A</li><li>Social Wall</li><li>Groups</li><li>Speaker list</li><li>Sponsor/Exhibitor will not be able to scan your name badges</li></ul>&nbsp;<br /><strong>Invisible activated</strong><br />If you do not accept these terms then you will not be visible in the app and the functionality will be limited to display only<br />&nbsp;<br />&nbsp;',
            ]);
        }

        $event_food_allergies = \App\Models\EventFoodAllergies::where('event_id', $request['from_event_id'])->get();
        
        if (count($event_food_allergies)) {
            foreach ($event_food_allergies as $record) {
                $record = $record->replicate();
                $record->event_id = $request['to_event_id'];
                $record->save();
            }
        } else {
            \App\Models\EventFoodAllergies::create([
                "event_id" => $request['to_event_id'],
                "subject" => "Food and allergies policy",
                "inline_text" => "I agree to {detail_link}food and allergies policy{/detail_link}",
                "description" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ]);

            \App\Models\EventFoodAllergies::create([
                "event_id" => $request['to_event_id'],
                "subject" => "General Data Protection Regulation (GDPR)",
                "inline_text" => "I agree to the GDPR. {detail_link} Read more.{/detail_link}",
                "description" => 'If you do not accept these terms only your "First Name" and "Last Name" will appear in following modules:<ul><li>Attendee</li><li>Chat</li><li>Survey</li><li>Polls</li><li>Q&amp;A</li><li>Social Wall</li><li>Groups</li><li>Speaker list</li><li>Sponsor/Exhibitor will not be able to scan your name badges</li></ul>&nbsp;<br /><strong>Invisible activated</strong><br />If you do not accept these terms then you will not be visible in the app and the functionality will be limited to display only<br />&nbsp;<br />&nbsp;',
            ]);
        }

        $event_ticket_setting = \App\Models\EventTicketSetting::where('event_id', $request['from_event_id'])->first();
        
        if ($event_ticket_setting) {
            $duplicate = $event_ticket_setting->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            \App\Models\EventTicketSetting::create([
                "event_id" => $request['to_event_id'],
                "show_price" => 1
            ]);
        }

        //Event Description
        $event_description = \App\Models\EventDescription::create([
            "event_id" => $request['to_event_id']
        ]);

        $from_event_description_info = \App\Models\EventDescriptionInfo::where('description_id', \App\Models\EventDescription::where("event_id", $request['from_event_id'])->value("id"))->get();
        
        if ($from_event_description_info) {
            foreach ($from_event_description_info as $description_info) {
                $description_info = $description_info->replicate();
                $description_info->description_id = $event_description->id;
                $description_info->save();
            }
        }
    }

    /**
     * Event pre installation / Event cloning
     *
     * @param array
     */
    public function preInstallation($request)
    {
        //Event attendee types
		$from_event_attendee_types = \App\Models\EventAttendeeType::where("event_id", $request['from_event_id'])->get();
		
		if ($from_event_attendee_types) {
			foreach ($from_event_attendee_types as $from_event_attendee_type) {
				$to_event_attendee_type = $from_event_attendee_type->replicate();
				$to_event_attendee_type->event_id = $request['to_event_id'];
				$to_event_attendee_type->save();
				session()->put('clone.event.event_attendee_types.' . $from_event_attendee_type->id, $to_event_attendee_type->id);
			}
		}

        //Registration forms
		$registration_forms = \App\Models\RegistrationForm::where('event_id', $request['from_event_id'])->get();
		
        if (count($registration_forms)) {
			foreach ($registration_forms as $record) {
				$to_record = $record->replicate();
				$to_record->event_id = $request['to_event_id'];
				if (session()->has('clone.event.event_attendee_types.' . $record->type_id)) {
					$to_record->type_id = session()->get('clone.event.event_attendee_types.' . $record->type_id);
				}
				$to_record->save();
                session()->put('clone.event.event_registration_form.' . $record->id, $to_record->id);
			}
		}
    }

    /**
     * Event copying data
     *
     * @param array
     */
    public function copyEventData($request)
    {
        //copy event data
        if ($request["content"]) {
            //Event categories
            $from_event_parent_categories = \App\Models\EventCategory::where("event_id", $request['from_event_id'])->where("parent_id", 0)->get();
            if ($from_event_parent_categories) {
                foreach ($from_event_parent_categories as $from_event_parent_category) {
                    $to_event_parent_category = $from_event_parent_category->replicate();
                    $to_event_parent_category->event_id = $request['to_event_id'];
                    $to_event_parent_category->save();

                    //category info 
                    $from_event_parent_category_info = \App\Models\EventCategoryInfo::where("category_id", $from_event_parent_category->id)->get();
                    foreach ($from_event_parent_category_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->category_id = $to_event_parent_category->id;
                        $info->save();
                    }

                    session()->put('clone.event.event_categories.' . $from_event_parent_category->id, $to_event_parent_category->id);

                    //child categories
                    $from_event_child_categories = \App\Models\EventCategory::where("event_id", $request['from_event_id'])->where("parent_id", $from_event_parent_category->id)->get();
                    if ($from_event_child_categories) {
                        foreach ($from_event_child_categories as $from_event_child_category) {
                            $to_event_child_category = $from_event_child_category->replicate();
                            $to_event_child_category->parent_id = $to_event_parent_category->id;
                            $to_event_child_category->event_id = $request['to_event_id'];
                            $to_event_child_category->save();

                            //child category info 
                            $from_event_child_category_info = \App\Models\EventCategoryInfo::where("category_id", $from_event_child_category->id)->get();
                            foreach ($from_event_child_category_info as $from_info) {
                                $info = $from_info->replicate();
                                $info->category_id = $to_event_child_category->id;
                                $info->save();
                            }

                            session()->put('clone.event.event_categories.' . $from_event_child_category->id, $to_event_child_category->id);
                        }
                    }
                }
            }

            //Event Sponsors
            $from_event_sponsors = \App\Models\EventSponsor::where("event_id", $request['from_event_id'])->get();
            if ($from_event_sponsors) {
                foreach ($from_event_sponsors as $from_event_sponsor) {
                    $to_event_sponsor = $from_event_sponsor->replicate();
                    $to_event_sponsor->event_id = $request['to_event_id'];
                    $to_event_sponsor->save();

                    //info 
                    $from_event_sponsor_info = \App\Models\SponsorInfo::where("sponsor_id", $from_event_sponsor->id)->get();
                    foreach ($from_event_sponsor_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->sponsor_id = $to_event_sponsor->id;
                        $info->languages_id = $request["languages"][0];
                        $info->save();
                    }

                    session()->put('clone.event.event_sponsors.' . $from_event_sponsor->id, $to_event_sponsor->id);

                    //Event Sponsor Categories
                    $from_event_sponsor_categories = \App\Models\EventSponsorCategory::where("sponsor_id", $from_event_sponsor->id)->get();
                    if ($from_event_sponsor_categories) {
                        foreach ($from_event_sponsor_categories as $from_event_sponsor_category) {
                            if (session()->has('clone.event.event_categories.' . $from_event_sponsor_category->category_id)) {
                                $to_event_sponsor_category = $from_event_sponsor_category->replicate();
                                $to_event_sponsor_category->category_id = session()->get('clone.event.event_categories.' . $from_event_sponsor_category->category_id);
                                $to_event_sponsor_category->sponsor_id = $to_event_sponsor->id;
                                $to_event_sponsor_category->save();
                            }
                        }
                    }

                    //Event Sponsor attendees => contact person
                    $from_event_sponsor_attendees = \App\Models\EventSponsorAttendee::where("sponsor_id", $from_event_sponsor->id)->get();
                    if ($from_event_sponsor_attendees) {
                        foreach ($from_event_sponsor_attendees as $from_event_sponsor_attendee) {
                            $to_event_sponsor_attendee = $from_event_sponsor_attendee->replicate();
                            $to_event_sponsor_attendee->sponsor_id = $to_event_sponsor->id;
                            $to_event_sponsor_attendee->save();
                        }
                    }
                }
            }

            //Event Exhibitors
            $from_event_exhibitors = \App\Models\EventExhibitor::where("event_id", $request['from_event_id'])->get();
            if ($from_event_exhibitors) {
                foreach ($from_event_exhibitors as $from_event_exhibitor) {
                    $to_event_exhibitor = $from_event_exhibitor->replicate();
                    $to_event_exhibitor->event_id = $request['to_event_id'];
                    $to_event_exhibitor->save();

                    //info 
                    $from_event_exhibitor_info = \App\Models\ExhibitorInfo::where("exhibitor_id", $from_event_exhibitor->id)->get();
                    foreach ($from_event_exhibitor_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->exhibitor_id = $to_event_exhibitor->id;
                        $info->languages_id = $request["languages"][0];
                        $info->save();
                    }

                    session()->put('clone.event.event_exhibitors.' . $from_event_exhibitor->id, $to_event_exhibitor->id);

                    //Event Exhibitor Categories
                    $from_event_exhibitor_categories = \App\Models\EventExhibitorCategory::where("exhibitor_id", $from_event_exhibitor->id)->get();
                    if ($from_event_exhibitor_categories) {
                        foreach ($from_event_exhibitor_categories as $from_event_exhibitor_category) {
                            if (session()->has('clone.event.event_categories.' . $from_event_exhibitor_category->category_id)) {
                                $to_event_exhibitor_category = $from_event_exhibitor_category->replicate();
                                $to_event_exhibitor_category->category_id = session()->get('clone.event.event_categories.' . $from_event_exhibitor_category->category_id);
                                $to_event_exhibitor_category->exhibitor_id = $to_event_exhibitor->id;
                                $to_event_exhibitor_category->save();
                            }
                        }
                    }

                    //Event Exhibitor attendees => contact person
                    $from_event_exhibitor_attendees = \App\Models\EventExhibitorAttendee::where("exhibitor_id", $from_event_exhibitor->id)->get();
                    if ($from_event_exhibitor_attendees) {
                        foreach ($from_event_exhibitor_attendees as $from_event_exhibitor_attendee) {
                            $to_event_exhibitor_attendee = $from_event_exhibitor_attendee->replicate();
                            $to_event_exhibitor_attendee->exhibitor_id = $to_event_exhibitor->id;
                            $to_event_exhibitor_attendee->save();
                        }
                    }
                }
            }

            //Event groups
            $from_event_parent_groups = \App\Models\EventGroup::where("event_id", $request['from_event_id'])->where("parent_id", 0)->get();
            if ($from_event_parent_groups) {
                foreach ($from_event_parent_groups as $from_event_parent_group) {
                    $to_event_parent_group = $from_event_parent_group->replicate();
                    $to_event_parent_group->event_id = $request['to_event_id'];
                    $to_event_parent_group->save();

                    //group info 
                    $from_event_parent_group_info = \App\Models\EventGroupInfo::where("group_id", $from_event_parent_group->id)->get();
                    foreach ($from_event_parent_group_info as $from_info) {
                        $info = $from_info->replicate();
                        $info->group_id = $to_event_parent_group->id;
                        $info->save();
                    }

                    session()->put('clone.event.event_groups.' . $from_event_parent_group->id, $to_event_parent_group->id);

                    //child groups
                    $from_event_child_groups = \App\Models\EventGroup::where("event_id", $request['from_event_id'])->where("parent_id", $from_event_parent_group->id)->get();
                    if ($from_event_child_groups) {
                        foreach ($from_event_child_groups as $from_event_child_group) {
                            $to_event_child_group = $from_event_child_group->replicate();
                            $to_event_child_group->parent_id = $to_event_parent_group->id;
                            $to_event_child_group->event_id = $request['to_event_id'];
                            $to_event_child_group->save();

                            //child group info 
                            $from_event_child_group_info = \App\Models\EventGroupInfo::where("group_id", $from_event_child_group->id)->get();
                            foreach ($from_event_child_group_info as $from_info) {
                                $info = $from_info->replicate();
                                $info->group_id = $to_event_child_group->id;
                                $info->save();
                            }

                            session()->put('clone.event.event_groups.' . $from_event_child_group->id, $to_event_child_group->id);
                        }
                    }
                }
            }
        }
    }

    /**
     * Attendee Events
     *
     * @param int
     */
    public static function attendee_events($attendee_id)
    {
        $result = \App\Models\EventAttendee::where('attendee_id', '=', $attendee_id)
            ->leftJoin('conf_events', 'conf_event_attendees.event_id', '=', 'conf_events.id')
            ->get();

        return $result;
    }

    /**
     * Save data for event
     *
     * @param array
     */

    public function store($formInput)
    {
        \DB::beginTransaction();

        try {

            $instance = $this->setCreateForm($formInput);

            $instance->create();

            $event = $this->getObject();

            $instance->updateEventUrl();

            //$instance->insertLanguages();

            $instance->insertInfo();

            $instance->save_description();

            $instance->addPackageDetail();

            $instance->addModulesOrder();

            $instance->cloneEvent(true);

            $instance->insertDateFormat();

            //$instance->billingItems();

            $instance->addSubAdminForEvent();

            $formInput['registration_form_id'] = $event->registration_form_id === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($event->id, 'attendee') : 0;

            $this->setFormInput($formInput);

            $instance->saveEventSiteSetting();

            $instance->saveEventWaitinglistSetting();

            //$instance->saveEventSetting();

            $instance->saveEventSiteBanners();
            
            self::add_module_progress(["event_id" => $event->id], "event");

            if($formInput['organizer_id'] == 133) { //insightevents organizer
                $this->addThirdPartySettings($formInput['organizer_id'],  $event);
            }
            
            \DB::commit();

            return $event;

        } catch (\Throwable $e) {
            \DB::rollback();
            throw $e;
        }
    }

    /**
     * Set form values for create event
     *
     * @param array
     */

    public function setCreateForm($formInput)
    {
        if(\Route::is('api-event-create')) {
            $orgainzer = $formInput['organizer'];
            $formInput['organizer_id'] = $orgainzer->id;
            $formInput['owner_id'] = super_organizer_id($orgainzer);
        } else {
            $orgainzer = organizer_info();
            $formInput['organizer_id'] = organizer_id();
            $formInput['owner_id'] = super_organizer_id();
        }

        $formInput['start_date'] = \Carbon\Carbon::parse($formInput['start_date'])->toDateString();
        $formInput['end_date'] = \Carbon\Carbon::parse($formInput['end_date'])->toDateString();

        $formInput['start_time'] = \Carbon\Carbon::parse($formInput['start_time'])->toTimeString();
        $formInput['end_time'] = \Carbon\Carbon::parse($formInput['end_time'])->toTimeString();

        $formInput['registration_end_date'] = (isset($formInput['registration_end_date']) && $formInput['registration_end_date'] ? \Carbon\Carbon::parse($formInput['registration_end_date'])->toDateString() : '0000-00-00 00:00:00');
        $formInput['cancellation_date'] = (isset($formInput['cancellation_date']) && $formInput['cancellation_date'] ? \Carbon\Carbon::parse($formInput['cancellation_date'])->toDateString() : '0000-00-00 00:00:00');
        $formInput['cancellation_end_time'] = (isset($formInput['cancellation_end_time']) ? \Carbon\Carbon::parse($formInput['cancellation_end_time'])->toTimeString() : '');

        $formInput['organizer_site'] = (isset($formInput['organizer_site']) && $formInput['organizer_site'] ? 1 : 0);
        $formInput['use_waitinglist'] = (isset($formInput['use_waitinglist']) && $formInput['use_waitinglist'] ? 1 : 0);
        $formInput['third_party_redirect'] = (isset($formInput['third_party_redirect']) && $formInput['third_party_redirect'] ? 1 : 0);
        $formInput['ga_setup'] = (isset($formInput['ga_setup']) && $formInput['ga_setup'] ? 1 : 0);
        $formInput['is_map'] = (isset($formInput['is_map']) && $formInput['is_map'] ? 1 : 0);

        if(!\Route::is('api-event-create')) {
            if (isset($formInput['from_event_id'])) {
                //Fetch package
                $formInput['assign_package_id'] =  \App\Models\AssignPackageUsed::where('event_id', $formInput['from_event_id'])->value('assign_package_id');
                $formInput['language_id'] = \App\Models\Event::where('id', $formInput['from_event_id'])->value('language_id');
            } else {
                $formInput['language_id'] = \App\Models\Event::where('id', $formInput['event_id'])->value('language_id');
            }
        }

        if (get_package_modules($formInput['organizer_id'], 'mobile_app')) {
            if ($orgainzer->show_native_app_link_all_events == 1) {
                $formInput['show_native_app_link'] = '1';
            }
        }

        $formInput['url'] = Str::slug($formInput['name'], '-');

        $formInput['dateformat'] = '1';

        $formInput['ticket_left'] = ($formInput['ticket_left'] ? $formInput['ticket_left'] : 0);

        if (isset($formInput['from_event_id'])) {
            $formInput['template_id'] = $formInput['from_event_id'];
        }

        $this->setFormInput($formInput);
        
        return $this;
    }

    /**
     * insert languages for event
     *
     */
    public function insertLanguages()
    {
        $formInput = $this->getFormInput();

        $event = $this->getObject();
        if (isset($formInput['language_id']) && $formInput['language_id']) {
            $event->languages()->attach($formInput['language_id'], array('status' => 1));
        }
        return $this;
    }


    public function updateEventUrl(){
        $event = $this->getObject();
        $event->url .= '-'.$event->id;
        $event->save();
        return $this;
    }
    /**
     * insert info for event
     *
     */
    public function insertInfo()
    {
        $event = $this->getObject();
        $formInput = $this->getFormInput();
        $info = array();
        if (isset($formInput['language_id'])) {
            foreach ($this->infoFields as $field) {
                if (isset($formInput[$field])) {
                    $info[] = new \App\Models\EventInfo(array('name' => $field, 'value' => $formInput[$field], 'languages_id' => $formInput['language_id'], 'status' => 1));
                }
            }
        }
        $event->info()->saveMany($info);

        /********     Email for google analytics request      ********/
        if (isset($formInput['ga_setup'])) {

            $organizer = organizer_info();

            // Google Analytics DB
            $analytics_requests = new \App\Models\AnalyticsRequest();
            $analytics_requests->event_code = $event->id;
            $analytics_requests->event_name = $event->name;
            $analytics_requests->organizer_id = $organizer->id;
            $analytics_requests->organizer_name = $organizer->first_name. ' '. $organizer->last_name;
            $analytics_requests->save();

            $this->data['subject'] = 'Google analytic API settings request!';
            $this->data['content'] = 'Google Analytic API Credential Request send by ' . $organizer->email . ' from this event "' . $event->name . '".';
            $this->data['view'] = 'email.plain-text';
            $this->data['from_name'] = $organizer->first_name;
            $this->data['bcc'] = ['ida@eventbuizz.com', 'mus@eventbuizz.com'];
            \Mail::to('ki@eventbuizz.com')->send(new Email($this->data));
        }

        return $this;
    }

    /**
     * insert / update event description
     *
     */
    public function save_description()
    {
        $event = $this->getObject();
        $formInput = $this->getFormInput();
        $languages = get_event_languages($event->id);
        $event_description = \App\Models\EventDescription::where('event_id', $event->id)->first();
        if ($event_description) {
            $info = \App\Models\EventDescriptionInfo::where('name', '=', 'description')->where('languages_id', $event->language_id)->where('description_id', $event_description->id)->first();
            if ($info) {
                $info->value = ($formInput['description'] ? $formInput['description'] : '');
                $info->languages_id = $event->language_id;
                $info->save();
            } else {
                \App\Models\EventDescriptionInfo::create([
                    "description_id" => $event_description->id,
                    "languages_id" => $event->language_id,
                    "name" => "description",
                    "value" => ($formInput['description'] ? $formInput['description'] : ''),
                ]);
            }
        } else {
            $event_description = \App\Models\EventDescription::create([
                "event_id" => $event->id
            ]);
            foreach ($languages as $language_id) {
                \App\Models\EventDescriptionInfo::create([
                    "description_id" => $event_description->id,
                    "languages_id" => $language_id,
                    "name" => "description",
                    "value" => ($formInput['description'] ? $formInput['description'] : ''),
                ]);
            }
        }
    }

    /**
     * insert package detail for event
     *
     */
    public function addPackageDetail()
    {
        $event = $this->getObject();
        $formInput = $this->getFormInput();
        if (isset($formInput['assign_package_id'])) {
            $assign_package = \App\Models\AssignPackage::find($formInput['assign_package_id']);
            $package = \App\Models\Package::find($assign_package->package_id);
            $duartion = $package->expire_duration;
            $no_of_events = $package->no_of_event;

            if ($no_of_events == 'Unlimited') {
                $expire_date = '0000-00-00 00:00:00';
            } else {
                $expire_date = date('Y-m-d', strtotime($event->end_date . ' + ' . $duartion . ' days'));
            }

            $used = new \App\Models\AssignPackageUsed(array('event_id' => $event->id, 'is_expire' => 'n', 'event_create_date' => date('Y-m-d'), 'event_expire_date' => $expire_date));
            $assign_package->packageUsed()->save($used);
        }
        return $this;
    }

    /**
     * insert modules for event
     *
     */
    public function addModulesOrder($from_event_id = null)
    {
        $formInput = $this->getFormInput();
        $fields_array_info = array('name');
        $modules_array = array();
        $event = $this->getObject();
        $event_languages = get_event_languages($event->id);
        $assign_package_addons = \App\Models\AssignPackage::find($formInput['assign_package_id'])->assignPackageAddons()->get();
        foreach ($assign_package_addons as $addon) {
            $addon_id = $addon['pivot']['addons_id'];
            $addon = \App\Models\Addon::find($addon_id);
            $modules_array[] = $addon->module_id;
        }

        $modules = \App\Models\Module::groupBy('group')->pluck('group')->toArray();

        foreach ($modules as $group) {
            $formInput['event_id'] = $event->id;
            $formInput['alies'] = $group;
            $formInput['status'] = 1;
            $formInput['created_at'] = \Carbon\Carbon::now();
            $formInput['updated_at'] = \Carbon\Carbon::now();
            $parent_label = \App\Models\ModuleGroup::create($formInput);
            foreach ($event_languages as $language_id) {
                $info['name'] = $group;
                $info['value'] = ucfirst($group);
                $info['group_id'] = $parent_label->id;
                $info['languages_id'] = $language_id;
                $info['created_at'] = \Carbon\Carbon::now();
                $info['updated_at'] = \Carbon\Carbon::now();
                \App\Models\ModuleGroupInfo::create($info);
            }
        }

        $sort_order = 0;
        foreach ($modules_array as $module_id) {
            if ($module_id != 0) {
                $module = \App\Models\Module::find($module_id);
                $modules_order = new \App\Models\EventModuleOrder();
                $modules_order->sort_order = $module->sort_order;
                $modules_order->event_id = $event->id;
                $modules_order->status = $module->status;
                $modules_order->alias = $module->alias;
                $modules_order->icon = get_module_images($module->alias);
                if ($modules_order->icon == '') $modules_order->icon = $module->alias . '.png';
                $modules_order->is_purchased = 1;
                $modules_order->group = $module->group;
                $modules_order->type = $module->type;
                $modules_order->version = $module->version;
                $modules_order->save();
                $module_order_info = array();
                $form_event_module = null;
                if($from_event_id !== null){
                    $form_event_module_order = \App\Models\EventModuleOrder::where('event_id', $from_event_id)->where('alias', $module->alias)->first();
                }

                foreach ($event_languages as $language) {
                    foreach ($fields_array_info as $field) {
                        if($from_event_id !== null && $form_event_module_order !== null){
                            $form_event_module_order_info = \App\Models\ModuleOrderInfo::where('module_order_id', $form_event_module_order->id)->first();
                            $module_order_info[] = new \App\Models\ModuleOrderInfo(array('name' => $form_event_module_order_info->name, 'value' => $form_event_module_order_info->value, 'languages_id' => $language, 'status' => $module->status));
                        }
                        else{
                            if ($language == '1') {
                                $module_order_info[] = new \App\Models\ModuleOrderInfo(array('name' => $module->$field, 'value' => $module->$field, 'languages_id' => $language, 'status' => $module->status));
                            } else {
                                $module_data = get_modules($language, $module_id);
                                if ($module_data) {
                                    $module_order_info[] = new \App\Models\ModuleOrderInfo(array('name' => $module_data[1], 'value' => $module_data[1], 'languages_id' => $language, 'status' => $module->status));
                                }
                            }
                        }
                    }
                }

                $module_order_object = \App\Models\EventModuleOrder::find($modules_order->id);
                $module_order_object->info()->saveMany($module_order_info);
                $sort_order++;
            }
        }

        //Insert Native App Modules Order
        foreach ($modules_array as $module_id) {
            if ($module_id != 0) {
                $module = \App\Models\Module::find($module_id);
                if ($module->alias == 'subregistration' && $module->type == 'frontend') $module->alias = 'my_subregistration';
                $modules_native = new \App\Models\EventNativeAppModule();
                $modules_native->sort = $module->sort_order;
                $modules_native->event_id = $event->id;
                $modules_native->status = $module->status;
                $modules_native->module_alias = $module->alias;
                $modules_native->save();
            }
        }

        return $this;
    }

    /**
     * update modules order info for event
     *
     */
    public function updateModulesOrder()
    {
        $formInput = $this->getFormInput();
        $event = $this->getObject();

        //update module order info
        if (isset($formInput['modules']) && is_array($formInput['modules'])) {
            $sort = 0;
            foreach ($formInput['modules'] as $module) {
                $event_modules_order = \App\Models\EventModuleOrder::where('event_id', $event->id)->where('alias', $module['alias'])->where('type', 'backend')->first();
                $event_modules_order->sort_order = $sort;
                $event_modules_order->status = $module['status'];
                $event_modules_order->save();

                \App\Models\ModuleOrderInfo::where('languages_id', $event->language_id)->where('module_order_id', $event_modules_order->id)->update([
                    "value" => $module['value']
                ]);
                $sort++;
            }
        }

        return $this;
    }

    /**
     * insert date format for event all language's
     *
     */
    public function insertDateFormat()
    {
        $event = $this->getObject();
        $current_lang = \App\Models\Language::find($event->language_id)->toArray();
        $event_languages[0] = $current_lang;
        $languages = $event->languages()->get();

        $i = 1;
        foreach ($languages as $lang) {
            $event_languages[$i] = array('id' => $lang['id'], 'name' => $lang['name'], 'lang_code' => $lang['lang_code'], 'ios_lang_code' => $lang['ios_lang_code'], 'status' => $lang['status'], 'created_at' => $lang['created_at'], 'updated_at' => $lang['updated_at'], 'deleted_at' => $lang['deleted_at']);
            $i++;
        }
        foreach ($event_languages as $row) {
            $format['event_id'] = $event->id;
            $format['language_id'] = $row['id'];
            $format['date_format_id'] = $row['id'];
            \App\Models\EventDateFormat::create($format);
        }

        return $this;
    }

    /**
     * insert billing items for event
     *
     */
    public function billingItems()
    {
        $event = $this->getObject();
        $this->eventsiteBillingItemRepository->insertDefaultEventAdminFee($event->id);
        return $this;
    }

    /**
     * insert sub admin for the event
     *
     */
    public function addSubAdminForEvent()
    {
        $organizer = organizer_info();
        $event = $this->getObject();
        if ($organizer->user_type == 'admin') {
            $sub_admin_event = new \App\Models\SubAdminEvent();
            $sub_admin_event->admin_id = $organizer->id;
            $sub_admin_event->event_id = $event->id;
            $sub_admin_event->save();
        }
    }

    /**
     * save event site setting for event
     *
     */
    public function saveEventSiteSetting()
    {
        $formInput = $this->getFormInput();
        $event = $this->getObject();
        unset($formInput['eventsite_banners']);
        $this->eventsiteRepository->saveSetting($event->id, $formInput, 'create');
    }
    
    /**
     * save event site setting for event
     *
     */
    public function saveEventWaitinglistSetting()
    {
        $formInput = $this->getFormInput();

        $event = $this->getObject();

        if(isset($formInput['registration_form_id']) && $formInput['registration_form_id'] > 0) {

            $settings = \App\Models\EventWaitingListSetting::where('event_id', $event->id)->where('registration_form_id', $formInput['registration_form_id'])->first();
    
            if($settings){
                $settings->after_stocked_to_waitinglist = $formInput['use_waitinglist'];
                //$settings->offerletter = $formInput['waitinglist_offerLetter'];
                //$settings->validity_duration = $formInput['waitinglist_validity_duration'];
                $settings->save();
            }
            else
            {
                \App\Models\EventWaitingListSetting::create([
                    "registration_form_id" => $formInput['registration_form_id'],
                    "after_stocked_to_waitinglist" => $formInput['use_waitinglist']
                    //"offerletter" => $formInput['waitinglist_offerLetter'],
                    //"validity_duration" => $formInput['waitinglist_validity_duration'],
                ]);
            }
            
        }

        return $this;
    }

    /**
     * save event setting for event
     *
     */
    public function saveEventSetting()
    {
        $formInput = $this->getFormInput();
        $event = $this->getObject();

        $header_logo = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'header_logo')->first();

        if (isset($formInput['header_logo']) && is_base64($formInput['header_logo'])) {
            $value = 'branding_header_logo_' . time() . '.png';
            //clone image
            copyFile(
                $formInput['header_logo'],
                config('cdn.cdn_upload_path') . 'assets/event/branding/' . $value
            );

            if ($header_logo) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/branding/' . $header_logo->value);
                $header_logo->value = $value;
                $header_logo->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'header_logo',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'header_logo');
        } else if ($header_logo) {
            $header_logo->value = ($formInput['header_logo'] ? fetchImageName($formInput['header_logo']) : '');
            $header_logo->save();
        }

        $app_icon = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'app_icon')->first();

        if (isset($formInput['app_icon']) && is_base64($formInput['app_icon'])) {
            $value = 'branding_app_icon_' . time() . '.png';
            //clone image
            copyFile(
                $formInput['app_icon'],
                config('cdn.cdn_upload_path') . 'assets/event/branding/' . $value
            );

            if ($app_icon) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/branding/' . $app_icon->value);
                $app_icon->value = $value;
                $app_icon->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'app_icon',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'app_icon');
        } else if ($app_icon) {
            $app_icon->value = ($formInput['app_icon'] ? fetchImageName($formInput['app_icon']) : '');
            $app_icon->save();
        }

        $fav_icon = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'fav_icon')->first();

        if (isset($formInput['fav_icon']) && is_base64($formInput['fav_icon'])) {
            $value = 'branding_fav_icon_' . time()  . '.png';
            //clone image
            copyFile(
                $formInput['fav_icon'],
                config('cdn.cdn_upload_path') . 'assets/event/branding/' . $value
            );

            if ($fav_icon) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/branding/' . $fav_icon->value);
                $fav_icon->value = $value;
                $fav_icon->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'fav_icon',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'favicon');
        } else if ($fav_icon) {
            $fav_icon->value = ($formInput['fav_icon'] ? fetchImageName($formInput['fav_icon']) : '');
            $fav_icon->save();
        }

        $social_media_logo = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'social_media_logo')->first();

        if (isset($formInput['social_media_logo']) && is_base64($formInput['social_media_logo'])) {
            $value = 'branding_social_media_logo_' . time() . '.png'; //clone image
            copyFile(
                $formInput['social_media_logo'],
                config('cdn.cdn_upload_path') . 'assets/event/social_media/' . $value
            );

            if ($social_media_logo) {
                //deleteFile(config('cdn.cdn_upload_path') . 'assets/event/social_media/' . $social_media_logo->value);
                $social_media_logo->value = $value;
                $social_media_logo->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'social_media_logo',
                    'value' => $value
                ]);
            }

            //insert into media library
            $this->mediaLibraryRepository->InsertDirectoryImage($value, 'social_media_logo');
        } else if ($social_media_logo && !$formInput['social_media_logo']) {
            $social_media_logo->value = ($formInput['social_media_logo'] ? fetchImageName($formInput['social_media_logo']) : '');
            $social_media_logo->save();
        }

        if (isset($formInput['primary_color']) && $formInput['primary_color']) {
            $primary_color = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'primary_color')->first();
            if ($primary_color) {
                $primary_color->value = $formInput['primary_color'];
                $primary_color->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'primary_color',
                    'value' => $formInput['primary_color']
                ]);
            }
        }

        if (isset($formInput['secondary_color']) && $formInput['secondary_color']) {
            $secondary_color = \App\Models\EventSetting::where('event_id', $event->id)->where('name', '=', 'secondary_color')->first();
            if ($secondary_color) {
                $secondary_color->value = $formInput['secondary_color'];
                $secondary_color->save();
            } else {
                \App\Models\EventSetting::create([
                    'event_id' => $event->id,
                    'name' => 'secondary_color',
                    'value' => $formInput['secondary_color']
                ]);
            }
        }
    }

    /**
     * save eventsite banners for event
     *
     */
    public function saveEventSiteBanners()
    {
        $formInput = $this->getFormInput();
        $event = $this->getObject();
        $fields = array('title', 'message');
        $languages = get_event_languages($event->id);
        if (isset($formInput['eventsite_banners']) && $formInput['eventsite_banners']) {
            foreach ($formInput['eventsite_banners'] as $key => $banner_img) {
                $image = 'event_site_banner_' . time() . $key . '.' . $banner_img->getClientOriginalExtension();
                $banner_img->storeAs('/assets/eventsite_banners', $image);

                //clone image
                moveFile(
                    storage_path('app/assets/eventsite_banners/' . $image),
                    config('cdn.cdn_upload_path') . 'assets/eventsite_banners/' . $image
                );

                $banner = \App\Models\EventSiteBanner::create([
                    'event_id' => $event->id,
                    'status' => 1,
                    'image' => $image
                ]);

                //info
                $info = array();
                foreach ($languages as $language) {
                    foreach ($fields as $field) {
                        $info[] = new \App\Models\EventSiteBannerInfo(array(
                            'name' => $field,
                            'value' => (isset($formInput[$field]) ? $formInput[$field] : ''),
                            'banner_id' => $banner->id, 'languages_id' => $language,
                            'status' => 1
                        ));
                    }
                }
                $banner->info()->saveMany($info);
            }
        }
        return $this;
    }

    /**
     * clone event
     *
     */
    public function cloneEvent($content = false)
    {
        //refresh session
        session()->forget("clone.event");
        
        $formInput = $this->getFormInput();

        $event = $this->getObject();
        
        $languages = get_event_languages($event->id);

        $data = array(
            [
                'from_event_id' => $formInput['from_event_id'],
                'to_event_id' => $event->id,
                'organizer_id' => $formInput['organizer_id'],
                'languages' => $languages,
                'content' => $content
            ]
        );

        $from_event = \App\Models\Event::where('id', $formInput['from_event_id'])->first();
        
        $event->registration_form_id = $from_event->registration_form_id;

        $event->save();

        //Dispatch event for cloning event
        event(CloneEvent::eventPreInstaller, $data);
        event(CloneEvent::eventCheckInOutInstaller, $data);
        event(CloneEvent::eventTemplateInstaller, $data);
        event(CloneEvent::eventSocialMediaInstaller, $data);
        event(CloneEvent::eventShareInstaller, $data);
        event(CloneEvent::eventDataInstaller, $data);
        event(CloneEvent::programInstaller, $data);
        event(CloneEvent::eventAttendeeInstaller, $data);
        event(CloneEvent::eventCompetitionInstaller, $data);
        event(CloneEvent::eventBrandingInstaller, $data);
        event(CloneEvent::eventDirectoryInstaller, $data);
        event(CloneEvent::eventBadgeInstaller, $data);
        event(CloneEvent::eventSiteInstaller, $data);
        event(CloneEvent::eventSettingInstaller, $data);
        event(CloneEvent::eventLabelsInstaller, $data);
        event(CloneEvent::eventSiteRegistrationInstaller, $data);
        event(CloneEvent::subRegistrationInstaller, $data);
        event(CloneEvent::eventInfoInstaller, $data);
        event(CloneEvent::eventSurveyInstaller, $data);
        event(CloneEvent::eventPollsInstaller, $data);
        event(CloneEvent::eventBillingItemInstaller, $data);
        event(CloneEvent::eventBillingVoucherInstaller, $data);
        event(CloneEvent::eventBillingHotelsInstaller, $data);
        event(CloneEvent::eventMapInstaller, $data);
        event(CloneEvent::eventSiteSettingInstaller, $data);
        event(CloneEvent::eventThemeInstaller, $data);
        event(CloneEvent::eventLeadsInstaller, $data);
    }

    /**
     *events listing
     * @param array
     */
    public function listing($formInput)
    {
        $organizer = organizer_info();
        $show_all_events = 0;
        $is_subadmin = $organizer->parent_id != 0 ? 1 : 0;

        //check subadmin and have all access.
        if($is_subadmin !== 0 && \Auth::user()->allow_plug_and_play_access && \Auth::user()->allow_admin_access && \Auth::user()->show_all_events){
            $show_all_events = 1;
        }

        $model = $this->model;
        $action = (isset($formInput['action']) && $formInput['action'] ? $formInput['action'] : 'active_future');
        $result = $model->where('organizer_id', organizer_id())->where('type', 1)->where('is_template', '<>', 1);

        // get only Subadmin Events
        if($is_subadmin && $show_all_events == 0){
            $event_ids = SubAdminEvent::where('admin_id', $organizer->id)->get(['event_id'])->toArray();
            $event_ids = array_column($event_ids, 'event_id');
            $result = $result->whereIn('id', $event_ids);
        }

        //Sorting
        if ((isset($formInput['order_by']) && $formInput['order_by']) && (isset($formInput['sort_by']) && $formInput['sort_by'])) {
            $result->orderBy($formInput['sort_by'], $formInput['order_by']);
        } else {
            $result->orderBy('start_date', 'ASC');
        }

        //search
        if (isset($formInput['query']) && $formInput['query']) {
            $result->where(function ($query) use ($formInput) {
                $query->where('id', 'LIKE', '%' . trim($formInput['query']) . '%')
                    ->orWhere('name', 'LIKE', '%' . trim($formInput['query']) . '%');
            });
        }

        if ($action == "active_future") {
            $result->where(function ($query) {
                $query->where('start_date', '>', \Carbon\Carbon::now()->format('Y-m-d'))
                    ->orwhere('end_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'));
            });
        } else if ($action == "active") {
            $result->where('start_date', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
            $result->where('end_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'));
        } else if ($action == "expired") {
            $result->where('end_date', '<', \Carbon\Carbon::now()->format('Y-m-d'));
        } else if ($action == "future") {
            $result->where('start_date', '>', \Carbon\Carbon::now()->format('Y-m-d'));
        }

        if (isset($formInput['limit']) && $formInput['limit']) $limit = $formInput['limit'];
        else $limit = 200;

        $result = $result->paginate($limit);

        $returnArray = array();
        foreach ($result as $event) {
            $event_info = $event->info()->get();
            $fields = array();
            foreach ($event_info as $info) {
                $fields[$info->name] = $info->value;
            }
            $owner = \App\Models\Organizer::where('id', '=', $event->owner_id)->first();
            $setting = \App\Models\EventSetting::where('event_id', $event->id)->where('name', 'header_logo')->first();
            $package = \App\Models\AssignPackageUsed::where('event_id', $event->id)->first();
            $eventsite_setting =  \App\Models\EventsiteSetting::where('event_id', $event->id)->where('registration_form_id', 0)->first();
            $assigned_attendees = \App\Models\EventAttendee::where('event_id', $event->id)->count();
            $event->start_date_time = \Carbon\Carbon::parse($event->start_date . ' ' . $event->start_time)->format('d/m/y H:i');

            $registered_attendees = AttendeeRepository::registered_attendees($event->id, true);

            $seats_remaning = ($eventsite_setting && $eventsite_setting->ticket_left && $eventsite_setting->ticket_left > $registered_attendees ? $eventsite_setting->ticket_left - $registered_attendees : 0);

            $default_template_id = \App\Models\EventEmailTemplate::where('event_id', $event->id)->where('alias', 'registration_invite')->where('type', 'email')->value('id');
            $event['default_template_id'] = $default_template_id;

            //$event['modules'] = EventSettingRepository::modules(['event_id' => $event->id, 'langauge_id' => $event->language_id], config('module.app_module_alias'));

            $event['registration_app_url'] = cdn('event/' . $event->url . '/detail');
            $event['mobile_app_url'] = cdn('event/' . $event->url);

            if ($eventsite_setting->payment_type == 1) {
                $event['registration_form_url'] = cdn('event/' . $event->url . '/detail/registration#get_register');
            } else {
                $event['registration_form_url'] = cdn('event/' . $event->url . '/detail/free/registration#get_register');
            }


            //Eventsite setting
            $event['eventsite_setting'] = $eventsite_setting;

            //Eventsite payment setting
            $event['eventsite_payment_setting'] = \App\Models\EventsitePaymentSetting::where('event_id', $event->id)->where('registration_form_id', 0)->first();

            $returnArray[] = array('event' => $event, 'info' => $fields, 'owner' => ($owner ? $owner->first_name . ' ' . $owner->last_name : ''), 'image' => ($setting ? $setting->value : ''), 'is_expire' => $package, 'total_tickets' => ($eventsite_setting && $eventsite_setting->ticket_left != 0 ? $eventsite_setting->ticket_left : '∞'), 'booked' => $assigned_attendees, 'registered_attendees' => $registered_attendees, 'invited' => AttendeeRepository::invited_attendees(['event_id' => $event->id, 'language_id' => $event->language_id], true), 'seats_remaning' => $seats_remaning, "cancelled_orders" => (int) EventsiteBillingOrderRepository::cancelledOrders($event->id, true), "waiting_list_orders" => (int) EventsiteBillingOrderRepository::waitingListOrders($event->id, true), "attendee_invitation_stats" => AttendeeRepository::attendee_invitation_stats(["event_id" => $event->id, "template_alias" => "registration_invite"]));
        }

        return array(
            'data' => $returnArray,
            'total' => $result->total(),
            'current_page' => $result->currentPage()
        );
    }

    /**
     *delete event
     * @param int
     */
    public function destroy($id)
    {
        return \App\Models\Event::where('id', $id)->delete();
    }

    /**
     *fetch event
     * @param array
     * @param int
     */
    public function fetchEvent($request_data, $id)
    {
        $event = \App\Models\Event::find($id);

        $response = array();

        if ($event) {

            $eventsite_form_setting = EventsiteRepository::getSetting(['event_id' => $event->id, 'registration_form_id' => (int)$request_data['registration_form_id']]);

            $waitinglist_form_setting = EventSiteSettingRepository::getWaitingListSetting(['event_id' => $event->id, 'registration_form_id' => (int)$request_data['registration_form_id']]);

            $event_description = \App\Models\EventDescription::where('event_id', $event->id)->with(['Info' => function ($query) use ($event) {
                return $query->where('languages_id', $event->language_id);
            }])->first();

            $infos = $event->info()->where('languages_id', $request_data['language_id'])->get();

            $fields = array();

            foreach ($infos as $info) {
                if ($info->name == 'domain_name') {
                    if ($info->value) {
                        $fields[$info->name][] = $info->value;
                    }
                } else {
                    $fields[$info->name] = $info->value;
                }
            }

            $event_settings = \App\Models\EventSetting::where('event_id', $id)->get();

            foreach ($event_settings as $setting) {
                if (in_array($setting->name, ["header_logo", "fav_icon", "app_icon"]) && $setting->value) {
                    $fields[$setting->name] = cdn('assets/event/branding/' . $setting->value);
                } else if (in_array($setting->name, ["social_media_logo"]) && $setting->value) {
                    $fields[$setting->name] = cdn('assets/event/social_media/' . $setting->value);
                } else if ($setting->value) {
                    $fields[$setting->name] = $setting->value;
                }
            }

            //default template id
            $default_template_id = \App\Models\EventEmailTemplate::where('event_id', $id)->where('alias', 'registration_invite')->where('type', 'email')->value('id');

            $event_data = $event->toArray();
            
            $event_data['default_template_id'] = $default_template_id;
            
            $event_data['readonly'] = (\Carbon\Carbon::parse(\Carbon\Carbon::now())->greaterThanOrEqualTo($event_data['start_date'])) ? true : false;
            
            $event_data['start_date'] = \Carbon\Carbon::parse($event_data['start_date'])->format('m/d/Y');
            
            $event_data['start_time'] = \Carbon\Carbon::parse($event_data['start_time'])->format('H:i');
            
            $event_data['end_date'] = \Carbon\Carbon::parse($event_data['end_date'])->format('m/d/Y');
            
            $event_data['end_time'] = \Carbon\Carbon::parse($event_data['end_time'])->format('H:i');
            
            $response['event'] = $event_data;
            
            $response['info'] = $fields;
            
            $response['info']['description'] = (isset($event_description['info'][0]['value']) ? $event_description['info'][0]['value'] : '');
            
            $response['eventsite_banners'] = \App\Models\EventSiteBanner::where('event_id', $id)->select('image', 'id')->get();
            
            $response['eventsite_setting'] = \App\Models\EventsiteSetting::where('event_id', $id)->where('registration_form_id', 0)->first();
            
            $response['eventsite_setting']['cancellation_date'] = (isset($response['eventsite_setting']['cancellation_date']) && !in_array($response['eventsite_setting']['cancellation_date'], ['1970-01-01 00:00:00', '0000-00-00 00:00:00']) ? \Carbon\Carbon::parse($response['eventsite_setting']->cancellation_date)->format('m/d/Y') : '');
            
            $response['eventsite_setting']['cancellation_end_time'] = (isset($response['eventsite_setting']['cancellation_end_time']) ? \Carbon\Carbon::parse($response['eventsite_setting']->cancellation_end_time)->format('H:i') : '');
            
            $response['eventsite_setting']['registration_end_date'] = (isset($eventsite_form_setting->registration_end_date) && !in_array($eventsite_form_setting->registration_end_date, ['1970-01-01 00:00:00', '0000-00-00 00:00:00']) ? \Carbon\Carbon::parse($eventsite_form_setting->registration_end_date)->format('m/d/Y') : "");
            
            $response['eventsite_setting']['registration_end_time'] = (isset($eventsite_form_setting->registration_end_time) ? \Carbon\Carbon::parse($eventsite_form_setting->registration_end_time)->format('H:i') : '');
            
            $response['eventsite_setting']['ticket_left'] = (int)$eventsite_form_setting->ticket_left;

            $response['eventsite_setting']['use_waitinglist'] = (int)$request_data['registration_form_id'] > 0 ? (int)$waitinglist_form_setting->after_stocked_to_waitinglist : (int)$eventsite_form_setting->use_waitinglist;
            
            $response['eventsite_setting']['third_party_redirect_url'] = (isset($response['eventsite_setting']->third_party_redirect_url) ? $response['eventsite_setting']->third_party_redirect_url : "");
            
            $response['eventsite_setting']['third_party_redirect'] = (isset($response['eventsite_setting']->third_party_redirect) ? $response['eventsite_setting']->third_party_redirect : 0);

            $response['event']['registration_app_url'] = cdn('event/' . $event->url . '/detail');

            $response['event']['mobile_app_url'] = cdn('event/' . $event->url);

            if ($response['eventsite_setting']->payment_type == 1) {
                $response['event']['registration_form_url'] = cdn('event/' . $event->url . '/detail/registration#get_register');
            } else {
                $response['event']['registration_form_url'] = cdn('event/' . $event->url . '/detail/free/registration#get_register');
            }

            //Eventsite setting
            $response['event']['eventsite_setting'] = $response['eventsite_setting'];

            //Eventsite payment setting
            $response['event']['eventsite_payment_setting'] = \App\Models\EventsitePaymentSetting::where('event_id', $id)->where('registration_form_id', 0)->first();

            //Eventsite secion fields
            $response['event']['eventsite_secion_fields'] = EventSiteSettingRepository::getAllSectionFields(["event_id" => $id, "language_id" => $event->language_id]);
            
            //Event Waiting list settings
            $response['event_waiting_list_settings'] = \App\Models\EventWaitingListSetting::where('event_id', $id)->first();

            //social media networks
            $social_media_networks = SocialMediaRepository::fetchSocialMedia(['event_id' => $id]);

            foreach ($social_media_networks as $network) {
                $response['social_media'][$network->name] = ($network->value ? $network->select_type . $network->value : '');
            }

        }
        
        return $response;
    }


    public function getEventDetails($inputForm, $id)
    {
        return Event::with(['description.info', 'eventsiteModules', 'eventsiteModules.info'=>function ($query) use ($inputForm) {return $query->where('languages_id', $inputForm['language_id']);}, 'eventsiteSettings', 'eventsiteSections', 'registration_site_theme', 'news_settings', 'timezone'])->find($id);
    }

    public function getMetaInfo($id)
    {
        return Event::with(['info', 'settings', 'eventsiteSettings'])->find($id);
    }

    /**
     * update data for event
     *
     * @param array
     * @param object
     */

    public function edit($formInput, $object)
    {
        $instance = $this->setCreateForm($formInput);

        $instance->update($object);

        $event = $this->getObject();

        $formInput['registration_form_id'] = $event->registration_form_id === 1 ? EventSiteSettingRepository::getAttendeeRegistrationFormByAlias($event->id, 'attendee') : 0;

        $this->setFormInput($formInput);

        $instance->updateEventUrl();

        //$instance->updateDateFormat();

        $instance->updateInfo();

        $instance->save_description();

        //$instance->saveEventSetting();

        $instance->saveEventSiteBanners();

        $instance->saveEventSiteSetting();

        $instance->saveEventWaitinglistSetting();

        return $this->getObject();
    }

    /**
     * update date format for current language
     *
     */
    public function updateDateFormat()
    {
        $formInput = $this->getFormInput();
        $event = $this->getObject();
        \App\Models\EventDateFormat::where('event_id', $event->id)->where('language_id', $formInput['language_id'])->update([
            "date_format_id" => $formInput['dateformat']
        ]);
        return $this;
    }

    /**
     * update info for event
     *
     */
    public function updateInfo()
    {
        $formInput = $this->getFormInput();
        $event = $this->getObject();
        if (isset($formInput['language_id'])) {
            foreach ($this->infoFields as $field) {
                if (isset($formInput[$field])) {
                    $info = \App\Models\EventInfo::where('languages_id', $formInput['language_id'])->where('event_id', $event->id)->where('name', $field)->first();
                    if ($info) {
                        $info->value = $formInput[$field];
                        $info->save();
                    } else {
                        \App\Models\EventInfo::create([
                            "languages_id" => $formInput['language_id'],
                            "event_id" => $event->id,
                            "name" => $field,
                            "value" => $formInput[$field],
                            "status" => 1
                        ]);
                    }
                }
            }
        }
    }

    /**
     *Organizer templates
     * @param array
     */
    public function templates($formData)
    {
        $model = $this->model;

        $query = $model->join('conf_eventsite_settings', "conf_eventsite_settings.event_id", "=", "conf_events.id")
            ->where('conf_events.organizer_id', organizer_id())
            ->where('conf_events.is_template', 1)
            ->where('conf_events.is_wizard_template', 1);

        if ($formData['paymentTypes']) $query->whereIn('conf_eventsite_settings.payment_type', $formData['paymentTypes']);

        if ($formData['filterLanguages']) $query->whereIn('conf_events.language_id', $formData['filterLanguages']);

        $query->where('conf_eventsite_settings.registration_form_id', 0);
        
        return $result = $query->select('conf_events.id', 'conf_events.name', 'conf_events.language_id')->groupBy('conf_events.id')->get()->toArray();
    }

    /**
     *Event calling code
     * @param array
     */
    static public function getEventCallingCode($formInput)
    {
        if (!isset($formInput['country_id'])) {
            $country_id = \App\Models\Event::where('id', $formInput['event_id'])->value('country_id');
        } else {
            $country_id = $formInput['country_id'];
        }

        $country = \App\Models\Country::where('id', $country_id)->first();

        if ($country) {
            return $event_calling_code = '+' . $country->calling_code;
        } else {
            return;
        }
    }

    /**
     *Event add module progress
     * @param array
     */
    static public function add_module_progress($formInput, $module)
    {
        $count = \App\Models\PlugnplayModulesProgress::where('event_id', $formInput['event_id'])->where('module', $module)->count();

        if ($count == 0) {
            \App\Models\PlugnplayModulesProgress::create([
                "event_id" => $formInput['event_id'],
                "module" => $module,
                "status" => 1
            ]);
        }
    }

    /**
     *Event fetch module progress
     * @param array
     */
    public function fetch_module_progress($formInput)
    {
        return \App\Models\PlugnplayModulesProgress::where('event_id', $formInput['event_id'])->pluck('module');
    }

    /**
     *Event groups
     * @param array
     */
    static public function getGroups($formInput, $ids = [], $label = false)
    {
        $groups = array();
        $query = \App\Models\EventGroup::where('event_id', $formInput['event_id'])->where('parent_id', '!=', '0')->with(['Info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput['language_id']);
        }]);
        if (!empty($ids)) $query->whereIn('id', $ids);
        $event_groups = $query->get()->toArray();
        foreach ($event_groups as $key => $event_group) {
            $id = $event_group['id'];
            $name = $event_group['info']['value'];
            if ($label) {
                $groups[$key]['value'] = $id;
                $groups[$key]['label'] = $name;
            } else {
                $groups[$key]['id'] = $id;
                $groups[$key]['name'] = $name;
            }
        }
        return $groups;
    }

    /**
     *copy event
     * @param array
     */

    public function copy($formInput)
    {
        \DB::beginTransaction();
        
        try {

            //Clone Event
            $from_event = \App\Models\Event::where('id', $formInput['from_event_id'])->first();
            $to_event = $from_event->replicate();
            $to_event->start_date = \Carbon\Carbon::parse($formInput['start_date'])->toDateString();
            $to_event->end_date = \Carbon\Carbon::parse($formInput['end_date'])->toDateString();
            $to_event->start_time = \Carbon\Carbon::parse($formInput['start_time'])->toTimeString();
            $to_event->end_time = \Carbon\Carbon::parse($formInput['end_time'])->toTimeString();
            $to_event->registration_flow_theme_id = $from_event->registration_flow_theme_id;
            $to_event->is_template =$formInput['is_template'];
            $to_event->is_advance_template =$formInput['is_advance_template'];
            $to_event->is_wizard_template =$formInput['is_wizard_template'];
            $to_event->name = $formInput["name"];
            $to_event->end_event_total_attendee_count = NULL;

            if ($formInput['user_type'] == 'admin') {
                $to_event->owner_id = $formInput['user_id'];
            }

            $to_event->save();

            //To get event id for new created event
            $to_event->url = Str::slug($formInput['name'], '-').'-'. $to_event->id;
            $to_event->save();

            //Event Info
            $from_event_info = \App\Models\EventInfo::where('event_id', $formInput['from_event_id'])->get();
            if ($from_event_info) {
                foreach ($from_event_info as $info) {
                    $info = $info->replicate();
                    $info->event_id = $to_event->id;
                    $info->save();
                }
            }

            //assign event sub admin
            if ($formInput['user_type'] == 'admin') {
                $sub_admin_event = new \App\Models\SubAdminEvent();
                $sub_admin_event->admin_id = $formInput['user_id'];
                $sub_admin_event->event_id = $to_event->id;
                $sub_admin_event->save();
            }


            //Assign Package
            $formInput['assign_package_id'] =  \App\Models\AssignPackageUsed::where('event_id', $formInput['from_event_id'])->value('assign_package_id');

            //create event object & set form data
            $instance = $this->setObject($to_event);

            $this->setFormInput($formInput);

            $instance->addPackageDetail();

            $instance->addModulesOrder($formInput['from_event_id']);

            $instance->cloneEvent(true);

            $instance->insertDateFormat();
            
            $instance->addSubAdminForEvent();

            //update eventsite settings
            $eventsite_setting = \App\Models\EventsiteSetting::where('event_id', $to_event->id)->where('registration_form_id', 0)->first();
            $eventsite_setting->registration_end_date = '';
            $eventsite_setting->cancellation_date = '';
            $eventsite_setting->save();

            \DB::commit();

            EventCloneLog::create(['from_event' => $from_event->id, 'to_event' => $to_event->id]);

            return $to_event;

        } catch (\Throwable $e) {
            \DB::rollback();
            throw $e;
        }
    }

    /**
     * @param mixed $formData
     *
     * @return [type]
     */
    public function cameraAccess($formData)
    {
        return \App\Models\EventAttendee::where('event_id', $formData['event_id'])->where('attendee_id', $formData['attendee_id'])->update([
            "camera" => $formData['camera']
        ]);
    }


    public function getAssignedAdmins($formInput, $event_id)
    {
        $sub_admin = Organizer::where('parent_id', request()->user()->id)->whereNotIn('user_type', ['super', 'demo'])
            ->whereHas('sub_admin_events', function(Builder $query) use ($event_id){
                $query->where('event_id', $event_id);
            });
        if(isset($formInput['assigned_query']) && !empty($formInput['assigned_query'])){
            $sub_admin = $sub_admin->where(function($query) use ($formInput){
                $query->where('email', 'like', '%'.$formInput['assigned_query'].'%')
                    ->orWhere('first_name', 'like', '%'.$formInput['assigned_query'].'%')
                    ->orWhere('last_name', 'like', '%'.$formInput['assigned_query'].'%');
            });
        }

        $sub_admin = $sub_admin->get()->toArray();
        return $sub_admin;
    }

    public function getUnassignedAdmins($formInput, $event_id){
        $sub_admin = Organizer::where('parent_id', request()->user()->id)->whereNotIn('user_type', ['super', 'demo'])
            ->whereHas('sub_admin_events', function(Builder $query) use ($event_id){
                $query->where('event_id', $event_id);
            }, '<', 1);

        if(isset($formInput['unassigned_query']) && !empty($formInput['unassigned_query'])){
            $sub_admin = $sub_admin->where(function($query) use ($formInput){
                $query->where('email', 'like', '%'.$formInput['unassigned_query'].'%')
                    ->orWhere('first_name', 'like', '%'.$formInput['unassigned_query'].'%')
                    ->orWhere('last_name', 'like', '%'.$formInput['unassigned_query'].'%');
            });
        }

        $sub_admin = $sub_admin->get()->toArray();

        return $sub_admin;
    }

    public function unassignAdmin(array $formInput, $event_id)
    {
        $event = Event::find($event_id);

        if($event){
            $event->sub_admins()->detach($formInput['keys']);
        }
    }

    public function assignAdmin(array $formInput, $event_id)
    {
        $event = Event::find($event_id);
        if($event){
            $event->sub_admins()->attach($formInput['keys']);
        }
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    static public function getEventDetail($formInput)
    {
        return \App\Models\Event::where('id', $formInput['event_id'])->with(['Info'])->first();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function getAssignedAttendees($formInput, $count = false)
    {
        $query = \App\Models\EventAttendee::where('event_id', $formInput['event_id']);

        if($count) {
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
    static public function getEventDateFormat($formInput)
    {
        $format = \App\Models\EventDateFormat::where('event_id', $formInput['event_id'])->where('language_id', $formInput['language_id'])->first();

        if($format->date_format_id == 2) {
            $date_format = 'd.m.Y';
        } elseif ($format->date_format_id == 5) {
            $date_format = 'Y-m-d';
        } else {
            $date_format = 'd-m-Y';
        }

        return $date_format;
    }

     /**
      * @param mixed $formInput
      *
      * @return [type]
      */
    static public function getEventAttendeeTypes($formInput)
    {
        return \App\Models\EventAttendeeType::where("event_id", $formInput['event_id'])->where('status', '=', '1')->where('languages_id', $formInput['language_id'])->orderBy('sort_order')->select('id', 'alias', 'attendee_type as name')->get();
    }

    /**
     *fetch event id
     * @param string
     */
    public function fetchEventId($slug)
    {
        return \App\Models\Event::where('url', $slug)->value('id');
    }

    /**
     *fetch event theme
     * @param int
     * @return
     */
    public function eventTheme($event_id)
    {
        return  Theme::has('currentEventTheme')->with(['modules' => function($query){
            $query->has('currentEventThemeModules');
        }])->first();

    }
    /**
     *fetch event banner theme
     * @param int
     * @return
     */
    public function eventThemeBanner($formInput)
    {
        $id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];
        return  \App\Models\EventSiteBanner::where('banner_type', 'top')
        ->where('event_id', $id)
        ->where('status', 1)
        ->with([ 'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },])
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    }


    function getMenuInfo($event_id){
        $language_id = get_event_languages($event_id);
        $top_menu = $this->getTopMenus($event_id, $language_id);
        $gallery_sub_menu = $this->getGallerySubMen($event_id, $language_id);
        $my_account_sub_menu = $this->getMyAccountSubMenu($event_id, $language_id);
        $additional_info_menu = $this->getAdditionalInfoMenu($event_id, $language_id);
        $general_info_menu = $this->getGeneralInfoMenu($event_id, $language_id);
        $practical_info_menu = $this->getPracticalInfoMenu($event_id, $language_id);
        $info_pages_menu = $this->getInformationPagesMenu($event_id, $language_id);

        return [
            'top_menu' => $top_menu,
            'gallery_sub_menu' => $gallery_sub_menu,
            'my_account_sub_menu' => $my_account_sub_menu,
            'additional_info_menu' => $additional_info_menu,
            'general_info_menu' => $general_info_menu,
            'practical_info_menu' => $practical_info_menu,
            'info_pages_menu' => $info_pages_menu
        ];
    }

    /**
     *get event filter top menu
    * @param int
    * @return
    */
    public   function filterTopMenu($data, $event_id, $language_id)
    {
        $i = 0;
        foreach ($data as $row) {
            $temp = array();

            if (count($row['info']) > 0) {
                foreach ($row['info'] as $val) {
                    $temp = $val['value'];
                }
            }

            if($row['alias'] === 'custom'){
                $cmsPages = \App\Models\PageBuilderPage::where('id', $temp)->first();
                $row['module'] = $cmsPages ? $cmsPages->name : $temp;
                $row['page_id'] = $temp;
                $data[$i] = $row;
                unset($data[$i]['info']);
            }
            else if($row['alias'] === 'info_pages'){
                $cmsPages = \App\Models\InformationSection::where('id', $temp)->with(['info' => function ($q) use($language_id) { $q->where("language_id", $language_id); }])->first();
                $row['module'] = $cmsPages ? $cmsPages->info[0]->value : $temp;
                $row['page_id'] = $temp;
                $data[$i] = $row;
                unset($data[$i]['info']);
            }
            else{
                $row['module'] = $temp;
                $data[$i] = $row;
                unset($data[$i]['info']);
            }

            if($row['alias'] === 'registration_packages'){
                $eventsite_setting = \App\Models\EventsiteSetting::where('event_id', $event_id)->where('registration_form_id', 0)->first();
                if($eventsite_setting['manage_package'] == 0){
                    unset($data[$i]);
                }
            }

            $i++;
        }
        return $data;
    }
    /**
     *get event top menu
    * @param int
    * @return
    */
    public  function getTopMenus($event_id, $language_id)
    {
        $data =  \App\Models\EventsiteTopMenu::where('event_id', $event_id)->where('alias', '<>', 'event_info')->where('alias', '<>', 'photos')->where('alias', '<>', 'login')->where('alias', '<>', 'register')->where('alias', '<>', 'videos')
        ->where('status', '=', '1')
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }])->whereNull('deleted_at')->orderBy('sort_order', 'asc')->get()->toArray();

        return $this->filterTopMenu($data, $event_id, $language_id);
    }
    /**
     *get event gallery menu
    * @param int
    * @return
    */
    public function getGallerySubMen($event_id, $language_id)
    {
        $data =  \App\Models\EventsiteTopMenu::where('event_id', '=', $event_id)->where('status', '=', 1)->where(function ($query) {
            $query->where('alias', '=', 'photos')->orWhere('alias', '=', 'videos');
        })
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }])->whereNull('deleted_at')->orderBy('sort_order', 'asc')->get()->toArray();

        return $this->filterTopMenu($data, $event_id, $language_id);
    }
    /**
     *get event account menu
    * @param int
    * @return
    */
    public  function getMyAccountSubMenu($event_id, $language_id)
    {
        $data =  \App\Models\EventsiteTopMenu::where('event_id', '=', $event_id)->where('status', '=', 1)->where(function ($query) {
            $query->where('alias', '=', 'login')->orWhere('alias', '=', 'register');
        })
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }])
            ->whereNull('deleted_at')->orderBy('sort_order', 'asc')->get()->toArray();

        return $this->filterTopMenu($data, $event_id, $language_id);
    }

    /**
     *get event additionalinfo menu
    * @param int
    * @return
    */
    public function getAdditionalInfoMenu($event_id, $language_id,$id = 0)
    {
        $folders = array();
        $menu_folders = \App\Models\AdditionalInfoMenu::where('event_id', '=', $event_id)
        ->where('parent_id', '=', $id)->where('status', '=', 1)->with(['Info' => function ($query) use ($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }])->get()->toArray();
        if (count($menu_folders) > 0) {
            foreach ($menu_folders as $folder) {
                $info = array();
                foreach ($folder["info"] as $item) {
                    $info[$item['name']] = $item['value'];
                }
                $folder['info'] = $info;

                $subMenu = \App\Models\AdditionalInfoPage::where('menu_id', '=', $folder["id"])->where('event_id', '=', $event_id)->where('status', '=', 1)
                    ->with(['info' => function ($query) use ($language_id) {
                        return $query->where('languages_id', '=', $language_id);
                    }])->orderBy("sort_order")->get()->toArray();

                    if(count($subMenu) >0)
                    {
                        foreach ($subMenu as $key => $menu) {
                        $info = array();
                        foreach ($menu["info"] as $item) {
                            $info[$item['name']] = $item['value'];
                        }
                        $subMenu[$key]['info'] = $info;
                    }

                    $folder['page_type'] = 'menu';
                    $folder["submenu"] = $subMenu;
                    $folders[] = $folder;
                    }
            }
        }


        $pages = array();
        $menu_pages = \App\Models\AdditionalInfoPage::where('menu_id', '=', $id)->where('event_id', '=', $event_id)->where('status', '=', 1)
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }])->get()->toArray();

        if (count($menu_pages) > 0) {
            foreach ($menu_pages as $menu_page) {
                $info = array();
                foreach ($menu_page["info"] as $item) {
                    $info[$item['name']] = $item['value'];
                }
                $menu_page['info'] = $info;
                $pages[] = $menu_page;
            }
        }

        $bothArrays = array();

        if (!empty($folders)  && !empty($pages)) {
            $bothArrays = array_merge($folders, $pages);
        } elseif (empty($folders)) {
            $bothArrays = $pages;
        } elseif (empty($pages)) {
            $bothArrays = $folders;
        }
        usort($bothArrays, "sortBySortOrder");
        return $bothArrays;
    }
    /**
     *get event Generalinfo menu
    * @param int
    * @return
    */
    public function getGeneralInfoMenu($event_id, $language_id,$id = 0)
    {
        $folders = array();
        $menu_folders = \App\Models\GeneralInfoMenu::where('event_id', '=', $event_id)
        ->where('parent_id', '=', $id)->where('status', '=', 1)->with(['Info' => function ($query) use ($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }])->get()->toArray();
        if (count($menu_folders) > 0) {
            foreach ($menu_folders as $folder) {
                $info = array();
                foreach ($folder["info"] as $item) {
                    $info[$item['name']] = $item['value'];
                }
                $folder['info'] = $info;

                $subMenu = \App\Models\GeneralInfoPage::where('menu_id', '=', $folder["id"])->where('event_id', '=', $event_id)->where('status', '=', 1)
                    ->with(['info' => function ($query) use ($language_id) {
                        return $query->where('languages_id', '=', $language_id);
                    }])->orderBy("sort_order")->get()->toArray();

                    if(count($subMenu) >0)
                    {
                        foreach ($subMenu as $key => $menu) {
                        $info = array();
                        foreach ($menu["info"] as $item) {
                            $info[$item['name']] = $item['value'];
                        }
                        $subMenu[$key]['info'] = $info;
                    }

                    $folder['page_type'] = 'menu';
                    $folder["submenu"] = $subMenu;
                    $folders[] = $folder;
                    }
            }
        }


        $pages = array();
        $menu_pages = \App\Models\GeneralInfoPage::where('menu_id', '=', $id)->where('event_id', '=', $event_id)->where('status', '=', 1)
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }])->get()->toArray();

        if (count($menu_pages) > 0) {
            foreach ($menu_pages as $menu_page) {
                $info = array();
                foreach ($menu_page["info"] as $item) {
                    $info[$item['name']] = $item['value'];
                }
                $menu_page['info'] = $info;
                $pages[] = $menu_page;
            }
        }

        $bothArrays = array();

        if (!empty($folders)  && !empty($pages)) {
            $bothArrays = array_merge($folders, $pages);
        } elseif (empty($folders)) {
            $bothArrays = $pages;
        } elseif (empty($pages)) {
            $bothArrays = $folders;
        }
        usort($bothArrays, "sortBySortOrder");
        return $bothArrays;
    }
    /**
     *get event Generalinfo menu
    * @param int
    * @return
    */
    public function getPracticalInfoMenu($event_id, $language_id,$id = 0)
    {
        $folders = array();
        $menu_folders = \App\Models\EventInfoMenu::where('event_id', '=', $event_id)
        ->where('parent_id', '=', $id)->where('status', '=', 1)->with(['Info' => function ($query) use ($language_id) {
            return $query->where('languages_id', '=', $language_id);
        }])->get()->toArray();
        if (count($menu_folders) > 0) {
            foreach ($menu_folders as $folder) {
                $info = array();
                foreach ($folder["info"] as $item) {
                    $info[$item['name']] = $item['value'];
                }
                $folder['info'] = $info;

                $subMenu = \App\Models\EventInfoPage::where('menu_id', '=', $folder["id"])->where('event_id', '=', $event_id)->where('status', '=', 1)
                    ->with(['info' => function ($query) use ($language_id) {
                        return $query->where('languages_id', '=', $language_id);
                    }])->orderBy("sort_order")->get()->toArray();

                    if(count($subMenu) >0)
                    {
                        foreach ($subMenu as $key => $menu) {
                        $info = array();
                        foreach ($menu["info"] as $item) {
                            $info[$item['name']] = $item['value'];
                        }
                        $subMenu[$key]['info'] = $info;
                    }

                    $folder['page_type'] = 'menu';
                    $folder["submenu"] = $subMenu;
                    $folders[] = $folder;
                    }
            }
        }


        $pages = array();
        $menu_pages = \App\Models\EventInfoPage::where('menu_id', '=', $id)->where('event_id', '=', $event_id)->where('status', '=', 1)
            ->with(['info' => function ($query) use ($language_id) {
                return $query->where('languages_id', '=', $language_id);
            }])->get()->toArray();

        if (count($menu_pages) > 0) {
            foreach ($menu_pages as $menu_page) {
                $info = array();
                foreach ($menu_page["info"] as $item) {
                    $info[$item['name']] = $item['value'];
                }
                $menu_page['info'] = $info;
                $pages[] = $menu_page;
            }
        }

        $bothArrays = array();

        if (!empty($folders)  && !empty($pages)) {
            $bothArrays = array_merge($folders, $pages);
        } elseif (empty($folders)) {
            $bothArrays = $pages;
        } elseif (empty($pages)) {
            $bothArrays = $folders;
        }
        usort($bothArrays, "sortBySortOrder");
        return $bothArrays;
    }

    /**
     * getEventLayoutSections
     *
     * @param  mixed $event_id
     * @param  mixed $layout_id
     * @return void
     */
    public function getEventLayoutSections($event_id, $layout_id)
    {
        $layoutSections = \App\Models\EventLayoutSection::where("event_id", $event_id)->where("layout_id", $layout_id)->orderBy("sort_order")->get();
        return $layoutSections;
    }

    /**
     * getEventModuleVariations
     *
     * @param  mixed $event_id
     * @param  mixed $theme_id
     * @param  mixed $layout_id
     * @return void
     */
    public function getEventModuleVariations($event_id, $theme_id, $layout_id)
    {
            $sections = \App\Models\EventLayoutSection::select(['module_alias', 'variation_slug'])->where("event_id", $event_id)->where("layout_id", $layout_id)->pluck('variation_slug', 'module_alias')->toArray();
            $moduleVariations= [];
            foreach ($sections as $key => $value) {
                $variation = \App\Models\EventThemeModuleVariation::where("event_id", $event_id)->where("theme_id", $theme_id)->where("alias", $key)->where("variation_slug", $value)->first();
                if($variation){
                    $moduleVariations[] = $variation;
                }
            }
            return $moduleVariations;
    }

    public function getsocialMediaShare($event_id, $lang_id)
    {
        $socialMedia = \App\Models\EventSiteSocialSection::where('event_id', '=', $event_id)
        ->whereNull('deleted_at')->orderBy('sort_order', 'asc')->get()->toArray();
        $newArr = [];
        foreach ($socialMedia as $value) {
            $newArr[$value['alias']] = $value['status'];
        }
        return $newArr;
    }

    public function getTotalAttendees($event_id)
    {
        $totalAttendeeCount = \App\Models\EventAttendee::where('event_id', $event_id)->count();
        return $totalAttendeeCount;
    }
    
    public function getWaitingListSettings($event_id)
    {
        $settings = \App\Models\EventWaitingListSetting::where('event_id', $event_id)->get()->toArray();
        return count($settings) > 0 ? $settings[0] : [];
    }

    public function getCustomSections($event_id)
    {
        $result = \App\Models\EventCustomHtml::where('event_id', $event_id)->whereNull('deleted_at')->get()->toArray();
        return count($result) > 0 ? $result[0] : [];
    }

    public function getNewsletterSubscriptionformSettings($subscription_id)
    {
        $result = \App\Models\MailingListInfo::select(['name', 'value'])->where('mailing_list_id', $subscription_id)->pluck('value', 'name')->toArray();
        return !empty($result) ? $result : [];
    }

    public function getEventDisclaimer($event)
    {
       $result = \App\Models\EventDisclaimer::where('event_id',  $event['id'])
        ->where('languages_id', $event['language_id'])->get()->toArray();
        return $result;
    }

    /**
	 *Event total attendees
	 * @param array
	 */
	static public function getTotalEventAttendees($formInput)
	{
		return \App\Models\EventAttendee::where('event_id', $formInput['event_id'])->count();
	}

    /**
     * checkEventDate
     *
     * @param  mixed $eventId
     * @param  mixed $date
     * @return void
     */
    public static function checkEventDate($eventId, $date)
    {
        $event_dates = \DB::select(DB::raw("select id from conf_events where ((start_date < '" . date('Y-m-d', strtotime($date)) . "' and end_date > '" . date('Y-m-d', strtotime($date)) . "') || start_date = '" . date('Y-m-d', strtotime($date)) . "' || end_date = '" . date('Y-m-d', strtotime($date)) . "') and id=" . $eventId . ""));

        if (empty($event_dates)) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * addThirdPartySettings
     *
     * @param  mixed $organizer_id
     * @param  mixed $from_event_id
     * @param  mixed $event_object
     * @return void
     */
    public function addThirdPartySettings($organizer_id, $event_object)
    {

        $thirdPartySettings = config("eventbuizz.insightEventsThirdPartyEventSetting");

        $newEvent = \App\Models\Event::where('organizer_id', '=', $organizer_id)
                    ->where('id', $event_object->id)
                    ->first()
                    ->toArray();

            foreach ($thirdPartySettings as $key => $settings) {

                $setting = \App\Models\EventSetting::where('event_id', '=', $newEvent['id'])
                    ->where('name', '=', $key)->first();
                if (isset($setting)) {
                    {
                        $setting->updated_at = \Carbon\Carbon::now();
                        $setting->value= $settings;
                        $setting->save();
                    }
                } else {
                    $formInput = [];
                    $formInput['event_id']= $newEvent['id'];
                    $formInput['name']= $key;
                    $formInput['value']= $settings;
                    $formInput['created_at'] = \Carbon\Carbon::now();
                    $formInput['updated_at'] = \Carbon\Carbon::now();
                    \App\Models\EventSetting::create($formInput);
                }

            }
            
        return true;
    }


    public function getInformationPagesMenu($event_id, $language_id,$id = 0)
    {
        $folders = array();
        $menu_folders = \App\Models\InformationSection::where('event_id', '=', $event_id)
        ->with(['info' => function ($query) use ($language_id) {
            return $query->where('language_id', '=', $language_id);
        }])->get()->toArray();

        if (count($menu_folders) > 0) {
            foreach ($menu_folders as $folder) {
                $info = array();
                foreach ($folder["info"] as $item) {
                    $info[$item['name']] = $item['value'];
                }
                $folder['info'] = $info;

                $subMenu = \App\Models\InformationPage::where('section_id', '=', $folder["id"])->where('event_id', '=', $event_id)->where(function($q){
                    $q->where('parent_id', null)->orWhere('parent_id', 0);
                })
                    ->with(['info' => function ($query) use ($language_id) {
                        return $query->where('language_id', '=', $language_id);
                    }, 'submenu', 'submenu.info'])->orderBy("sort_order")->get()->toArray();

                    if(count($subMenu) >0)
                    {

                    $subMenu = $this->mapInfoRecursive($subMenu);

                    $folder['page_type'] = 'menu';
                    $folder["submenu"] = $subMenu;
                    $folders[] = $folder;
                    }
            }
        }

        return $folders;
    }

    function mapInfoRecursive($data){
        foreach ($data as $key => $menu) {
            $info = array();
            foreach ($menu["info"] as $item) {
                $info[$item['name']] = $item['value'];
            }
            $data[$key]['info'] = $info;
            if(isset($menu['submenu']) && count($menu['submenu']) > 0){
                $data[$key]['submenu'] = $this->mapInfoRecursive($menu['submenu']);
            }
        }
        return $data;
    }

    public function eventSortableBanner($formInput)
    {
        $id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];
        return  \App\Models\EventSiteBanner::where('banner_type', 'banner')
        ->where('event_id', $id)
        ->where('status', 1)
        ->with([ 'info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },])
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    }

    public function getAllEventContactPersons($event_id){
        return \App\Models\EventContactPerson::where('event_id', $event_id)->get();
    }
    
    public function getAllEventOpeningHours($event_id){
        return \App\Models\EventOpeningHour::where('event_id', $event_id)->get();
    }
    
    public function getEventCountry($country_id)
    {
        $country_name = "";
        $settings = \App\Models\Country::where('id',  $country_id)->first();

        if($settings){
            $country_name = $settings->name;
        }

        return $country_name;
    }
    
    /**
     * getEventAttendeeType
     *
     * @param  mixed $formInput
     * @return void
     */
    static public function getEventAttendeeType($formInput)
    {
        return \App\Models\EventAttendeeType::where("event_id", $formInput['event_id'])->where('id', $formInput['id'])->where('languages_id', $formInput['language_id'])->select('id', 'alias', 'attendee_type as name')->first();
    }

    public static function checkifRegistrationEndDatePassed($event_id)
    {
        $registration_end_date_passed = 1;

        $registration_form_ids = \App\Models\RegistrationForm::where('event_id', $event_id)->where('status', 1)->pluck('id');

        $eventsite_settings = \App\Models\EventsiteSetting::where('event_id', $event_id)->whereIn('registration_form_id', $registration_form_ids)->get();
        
        foreach ($eventsite_settings as $key => $settings) {
            if($settings['registration_end_date'] === "0000-00-00 00:00:00"){
                $registration_end_date_passed = 0;
                break;
            }
            
            $currentDate = \Carbon\Carbon::now();
            $end_date = date('Y-m-d', strtotime($settings['registration_end_date']));
            $end_time = date('H:i:s', strtotime($settings['registration_end_time']));
            $combinedDT = date('Y-m-d H:i:s', strtotime("$end_date $end_time"));
            $parsed_date = Carbon::parse($combinedDT);

            if($currentDate->lt($parsed_date)){
                $registration_end_date_passed = 0;
                break;
            }
        }

        return $registration_end_date_passed;

    }
    public static function registrationFormInfo($event_id)
    {
        $has_multiple_form = false;
        $form_registration_end_date = '';
        $form_registration_remaining_tickets = '';
        $registration_forms = \App\Models\RegistrationForm::where(['event_id' => $event_id, 'status' => 1])->get()->toArray();
        if (count($registration_forms) > 1) {
            $has_multiple_form = true;
        } else {

            $form_setting = \App\Models\EventsiteSetting::where('registration_form_id', $registration_forms[0]['id'])
            ->first();
            $event_setting = \App\Models\EventsiteSetting::where('event_id', $event_id)->where('registration_form_id', 0)
            ->first();
            if ($form_setting->registration_end_date != "0000-00-00 00:00:00" && $event_setting->eventsite_time_left === 1) {
                $currentDate = \Carbon\Carbon::now();
                $end_date = date('Y-m-d', strtotime($form_setting->registration_end_date));
                $end_time = date('H:i:s', strtotime($form_setting->registration_end_time));
                $combinedDT = date('Y-m-d H:i:s', strtotime("$end_date $end_time"));
                $parsed_date = Carbon::parse($combinedDT);
                if ($currentDate->lt($parsed_date)) {
                    $form_registration_end_date = \Carbon\Carbon::parse($form_setting->registration_end_date)->format('Y-m-d') . ' ' . $form_setting->registration_end_time;
                }
            }
            if($form_setting->ticket_left > 0 && $event_setting->eventsite_tickets_left === 1){
                $active_orders_ids = EventsiteBillingOrderRepository::activeOrders(['event_id' =>  $event_id, 'status' => ['draft', 'completed'], 'waiting_list' => 0], false, true);
                //Validate form stock
                $soldTickets = EventsiteBillingOrderRepository::getOrderAssignedAttendees(['order_ids' =>  $active_orders_ids, 'registration_form_id' => $registration_forms[0]['id']], true);
                
                $form_registration_remaining_tickets = (int) $form_setting->ticket_left - (int) $soldTickets;
            }
        }
        return array(
            'has_multiple_form' => $has_multiple_form,
            'form_registration_end_date' => $form_registration_end_date,
            'form_registration_remaining_tickets' => $form_registration_remaining_tickets,
        );
    }
}
