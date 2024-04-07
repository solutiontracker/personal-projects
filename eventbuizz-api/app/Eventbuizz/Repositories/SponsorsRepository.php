<?php

namespace App\Eventbuizz\Repositories;

use App\Models\EventSponsor;
use App\Models\EventSponsorCategory;
use App\Models\SponsorSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
class SponsorsRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    /**
     * getImportSettingsReservations
     *
     * @return void
     */
    static public function getImportSettingsReservations()
    {
        $settings = array(
            'fields' => array(
                'date' => array(
                    'field' => 'date',
                    'label' => 'Date',
                    'required' => true
                ),
                'time_from' => array(
                    'field' => 'time_from',
                    'label' => 'Time From',
                    'required' => true
                ),
                'time_to' => array(
                    'field' => 'time_to',
                    'label' => 'Time To',
                    'required' => true
                ),
                'duration' => array(
                    'field' => 'duration',
                    'label' => 'Duration',
                    'required' => true
                ),
                'entity_id' => array(
                    'field' => 'entity_id',
                    'label' => 'Sponsor ID',
                    'required' => true
                ),
                'sponsor_name' => array(
                    'field' => 'sponsor_name',
                    'label' => 'Sponsor Name',
                    'required' => false
                ),



            )
        );
        
        return $settings;
    }

    /**
     *Delete Attendee Sponsors Info
     *
     * @param int or @param array
     * @param int
     */
    public static function deleteSponsorContact($attendee_id, $event_id)
    {
        $sponsor_ids = \App\Models\EventSponsor::where('event_id', '=', $event_id)->pluck('id')->toArray();
        if (is_array($attendee_id)) {
            \App\Models\EventSponsorAttendee::whereIn('sponsor_id', $sponsor_ids)->whereIn('attendee_id', $attendee_id)->delete();
        } else {
            \App\Models\EventSponsorAttendee::whereIn('sponsor_id', $sponsor_ids)->where('attendee_id', '=', $attendee_id)->delete();
        }
    }

    /**
     *sponsors
     *
     * @param array
     * @return array
     */
    static public function getSponsors($formInput)
    {
        $array = array();
        $results = \App\Models\EventSponsor::where('event_id', $formInput['event_id'])->get();
        foreach ($results as $row) {
            $array[$row['id']] = $row->name;
        }
        return $array;
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function fetchEventId($slug)
    {
        return \App\Models\Event::where('url', $slug)->value('id');
    }


    public function getSponsorSettings($event_id){
        $SponsorsSetting = SponsorSetting::where('event_id', '=', $event_id)->first();
        return $SponsorsSetting;
    }
    
    /**
     * getEventSiteSponsorsListing
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getEventSiteSponsorsListing($formInput){

        $event_id = $formInput['event_id'];

        $lang_id = $formInput['language_id'];

        $category_id = $formInput['category_id'];

        $query = $formInput['query'];

        if(isset($formInput['event']['sponsor_settings']) && $formInput['event']['sponsor_settings']) {
            $sponsor_settings = $formInput['event']['sponsor_settings'];
        } else {
            $sponsor_settings = self::getSetting($event_id);
        }
        
        $sponsors = EventSponsor::with(['categories', 'attendeeSponsors', 'categories.info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); },])->where('event_id', $event_id);

        if($category_id > 0) {
            $sponsors = $sponsors->whereHas('categories',function ($query) use($category_id) {
                return $query->where('conf_event_sponsor_categories.category_id', $category_id);
            });
        }

        if($query) {
            $sponsors = $sponsors->where('name', 'like', $query . '%');
        }

        $sponsors = $sponsors->orderBy('name', 'ASC')->get()->toArray();
        
        foreach ($sponsors as $key => $value) {

            $value = $this->refactorSponsorData($value, $sponsor_settings);

            $sponsors[$key] = $value;

            if(count($value['categories']) > 0) {
                $sponsors[$key]['sort_order'] = $value['categories'][0]['sort_order'];
                foreach($value['categories'] as $i => $category) {
                    $sponsors[$key]['categories'][$i]['info'] = readArrayKey($category, [], 'info');
                }
            }else{
                $sponsors[$key]['sort_order'] = 5000;
            }

        }
        
        if($sponsor_settings['sortType'] == 1){
            $sort = array();
            foreach($sponsors as $k=>$v) {
                $sort['name'][$k] = $v['name'];
                $sort['sort_order'][$k] = $v['sort_order'];
            }
            array_multisort($sort['sort_order'], SORT_ASC,$sort['name'], SORT_ASC,SORT_NATURAL|SORT_FLAG_CASE, $sponsors);
        }

        return $sponsors;
    }
        
    /**
     * getSponsorCategories
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getSponsorCategories($formInput)
    {
        $sponsorCategories = \App\Models\EventCategory::where("event_id", $formInput['event_id'])->where('cat_type', 'sponsors')->with(['info'])->orderBy('sort_order')->get()->toArray();
        foreach ($sponsorCategories as $key => $category) {
            foreach ($category['info'] as  $info) {
                $category[$info['name']] = $info['value'];
            }
            unset($category['info']);
            $sponsorCategories[$key] = $category;
        }
        return $sponsorCategories;
    }
    

    /**
     * @param mixed $sponsor
     * @param mixed $settings
     * @return mixed
     */
    public function refactorSponsorData($sponsor, $settings)
    {
		$sponsor['email'] = $settings['sponsorEmail'] === 1 ? $sponsor['email'] : "";

		$sponsor['phone_number'] = $settings['sponsorPhone'] === 1 ? $sponsor['phone_number'] : "";

        $sponsor['booth'] = $settings['show_booth'] === 1 ? $sponsor['booth'] : "";

		$sponsor['show_cat'] = $settings['catTab'] === 1 ? true : false;

        if($sponsor['info']){
            foreach ($sponsor['info'] as $key => $value) {
                $sponsor[$value['name']] = $value['value'];
            }
            unset($sponsor['info']);
        }

		return $sponsor;
    }
    
    /**
     * sortSponsorOnCategoryByName
     *
     * @param  mixed $a
     * @param  mixed $b
     * @return void
     */
    public function sortSponsorOnCategoryByName($a, $b){

        $c1 =($a['categories'][0]['info'][0]['value'] ?? '');
        $c2 = ($b['categories'][0]['info'][0]['value'] ?? '');

        if($c1 == ''){
            return 1;
        }
        if($c2 == ''){
            return -1;
        }
        $name1 =  strtolower($c1 .' '. $a['name']);
        $name2 =  strtolower($c2.' '. $b['name']);

        return $name1 <=> $name2;
    }

    
    /**
     * getEventSiteSponsors
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getEventSiteSponsors($formInput)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];
        $sponsorCategories = \App\Models\EventCategory::where("event_id", $event_id)->where('cat_type', 'sponsors')->where('parent_id', 0)->with(['info' => function ($q) use ($lang_id) {
            return $q->where('languages_id', $lang_id);
        }])->orderBy('sort_order')->get()->toArray();
        foreach ($sponsorCategories as $key => $sponsorCategory) {
            $categorySponsor = \App\Models\EventSponsor::where("event_id", $event_id)->whereHas("categories", function ($q) use ($sponsorCategory) {
                return $q->where('category_id', $sponsorCategory['id']);
            })->with(['info' => function ($q) use ($lang_id) {
               return $q->where('languages_id', $lang_id);
            }])->get()->toArray();
            $sponsorCategories[$key]['sponsors'] = $categorySponsor;
        }
        $withoutCategories = \App\Models\EventSponsor::where("event_id", $event_id)->whereDoesntHave("categories")->with(['info' => function ($q) use ($lang_id) {
            return $q->where('languages_id', $lang_id);
         }])->get()->toArray();
        if (!empty($withoutCategories)) {
            $sponsorCategories[]['sponsors'] = $withoutCategories;
        }
        foreach ($sponsorCategories as $key => $category) {
            foreach ($category['info'] as $info) {
                    $category[$info['name']] = $info['value'];
            }
            unset($category['info']);
            foreach ($category['sponsors'] as $ikey => $sponsor) {
                    foreach ($sponsor['info'] as $info) {
                        $sponsor[$info['name']] = $info['value'];
                    }
                    unset($sponsor['info']);
                $category['sponsors'][$ikey] = $sponsor;
            }
            $sponsorCategories[$key] = $category;
        }
        return $sponsorCategories;
    }

    public function getEventSiteSponsorDetail($formInput, $sponsor_id)
    {   
        $event_id = $formInput['event_id'];

        $lang_id = $formInput['language_id'];

        $sponsor_settings = $formInput['event']['sponsor_settings'];

        $speaker_settings = $formInput['event']['speaker_settings'];

        $attendee_settings = $formInput['event']['attendee_settings'];

        $sponsor = \App\Models\EventSponsor::where('id', $sponsor_id)->with([
            'info' => function ($q) use ($lang_id) {
                $q->where('languages_id', $lang_id);
            }    
        ]);

        if($sponsor_settings['show_contact_person']){
            $sponsor = $sponsor->with(['sponsorsAttendee.attendees' => function ($query) {
                return $query->select('id', 'first_name', 'last_name', 'image', 'email');
            }, 'sponsorsAttendee.attendees.info' => function ($query) use($lang_id) {
                return $query->where(function ($q)  {
                    return $q->where('name','company_name')
                             ->orWhere('name',  'title')
                             ->orWhere('name',  'phone')
                             ->orWhere('name',  'website')
                             ->orWhere('name',  'facebook')
                             ->orWhere('name',  'linkedin')
                             ->orWhere('name',  'twitter')
                             ->orWhere('name',  'facebook_protocol')
                             ->orWhere('name',  'twitter_protocol')
                             ->orWhere('name',  'linkedin_protocol');
                })->where('languages_id',  $lang_id);
            }, 'sponsorsAttendee.attendees.currentEventAttendee',]);
        
        }

        $sponsor = $sponsor->where('event_id', $event_id);

        $sponsor = $sponsor->first();

        $sponsor = $sponsor ? $sponsor->toArray() : null;

        if($sponsor !== null) {

            $sponsor = $this->refactorSponsorData($sponsor, $sponsor_settings);

            if ($sponsor_settings['show_contact_person']) {

                $temp_array = [];

                foreach ($sponsor['sponsors_attendee'] as $key => $contact_person) {

                    $gdpr = $formInput['event']['gdpr_settings']['enable_gdpr'] ? $contact_person['attendees']['current_event_attendee']['gdpr'] : 0;

                    if ($gdpr == 1) {

                        if ($contact_person['attendees']['current_event_attendee']['speaker']) {
                            $attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($event_id, 'speaker', $lang_id);
                            $contact_person = AttendeeRepository::refactorDataByAttendeeTypeSettings($event_id, $contact_person['attendees'], $attendee_type_id);
                        } else if ($contact_person['attendees']['current_event_attendee']['exhibitor']) {
                            $attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($event_id, 'exhibitor', $lang_id);
                            $contact_person = AttendeeRepository::refactorDataByAttendeeTypeSettings($event_id, $contact_person['attendees'], $attendee_type_id);
                        } else if ($contact_person['attendees']['current_event_attendee']['sponser']) {
                            $attendee_type_id = AttendeeRepository::getAttendeeTypeIDByRole($event_id, 'sponsor', $lang_id);
                            $contact_person = AttendeeRepository::refactorDataByAttendeeTypeSettings($event_id, $contact_person['attendees'], $attendee_type_id);
                        } else {
                            $contact_person = AttendeeRepository::refactorDataByAttendeeTypeSettings($event_id, $contact_person['attendees'], $contact_person['attendees']['current_event_attendee']['attendee_type']);
                        }

                        $contact_person['info'] = readArrayKey($contact_person, [], 'info');
                        
                        $temp_array[] = $contact_person;

                    } else {

                        $temp_array[] = [
                            "id" => $contact_person['attendees']['id'],
                            "first_name" => $contact_person['attendees']['first_name'],
                            "last_name" => $contact_person['attendees']['last_name'],
                        ];

                    }

                }

                $sponsor['sponsors_attendee'] = $temp_array;

            }

        }
         
        return $sponsor;

    }

    public function getEventAttendee($attendee_id, $event_id)
    {
        $result = \App\Models\EventAttendee::where('attendee_id', $attendee_id)->where('event_id', $event_id)->first();
        return $result;
    }

    public function getSponsorDocument($formInput, $sponsor_id)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];

        $parentDirectory = \App\Models\Directory::where('event_id', $event_id)->where('sponsor_id', $sponsor_id)->first();
           $parentDirectory = $parentDirectory !== null ? $parentDirectory->toArray() : [];

        $id = $parentDirectory['id'];

        $directories = \App\Models\Directory::where('event_id', '=', $event_id)->where('id', '=', $id)->with(['info' => function ($query) use($lang_id) {
            return $query->where('languages_id', $lang_id);
        }])->with(['files.info' => function ($query) use($lang_id) {
            return $query->where('languages_id', $lang_id);
        }])->with('children.files')->orderby('sort_order', 'ASC')->get()->toArray();

        foreach ($directories as $key => $value) {
			foreach ($value['info'] as $item) {
				$value[$item['name']] = $item['value'];
			}
			unset($value['info']);
			foreach ($value['files'] as $ikey => $file) {
				foreach ($file['info'] as $item) {
					$file[$item['name']] = $item['value'];
				}
				unset($file['info']);

                if ($file['s3'] == 1) {
                    $file['s3_url'] = getS3Image('assets/directory/' . $file['path']);
                }
                
				$value['files'][$ikey] = $file;

			}
			$directories[$key] = $value; 
            $directories[$key]['children'] = $this->getBreadcrumb($value['id'], $event_id, $lang_id);
            $new_array = array_values(Arr::sort(array_merge($directories[$key]['children'], $value['files']), function($value)
            {
                return $value['sort_order'];
            }));
			$directories[$key]['children_files'] = $new_array;
		}

        return $directories;

    }

    public function getBreadcrumb($id, $event_id, $lang_id)
    {

        $directories = \App\Models\Directory::where('event_id', '=', $event_id)->where('parent_id',  $id)->with(['info' => function ($query) use($lang_id) {
			return $query->where('languages_id', $lang_id);
		}])->with(['files', 'files.info' => function ($query) use($lang_id) {
            return $query->where('languages_id', $lang_id);
        }])->orderby('sort_order', 'ASC')->get()->toArray();

		foreach ($directories as $key => $dir) {
			foreach ($dir['info'] as $item) {
				$dir[$item['name']]=$item['value'];
			}
			unset($dir['info']);
			foreach ($dir['files'] as $ikey => $file) {
				foreach ($file['info'] as $info) {
					$file[$info['name']]=$info['value'];
				}
				unset($file['info']);
                
                if ($file['s3'] == 1) {
                    $file['s3_url'] = getS3Image('assets/directory/' . $file['path']);
                }

				$dir['files'][$ikey] = $file;
			}
            $directories[$key]['children'] = $this->getBreadcrumb($dir['id'], $event_id, $lang_id);
            $new_array = array_values(Arr::sort(array_merge($directories[$key]['children'], $dir['files']), function($value)
            {
                return $value['sort_order'];
            }));
            $directories[$key] = $dir; 
			$directories[$key]['children_files'] = $new_array; 
		}
		return $directories;
    }

    /*
     * getSetting
     *
     * @param  mixed $event_id
     * @return void
     */
    public static function getSetting($event_id) {
        return \App\Models\SponsorSetting::where('event_id',$event_id)->get()->first();
    }

    /**
     * sponsorsCreateSlot
     *
     * @param  mixed $eventId
     * @param  mixed $sponsorId
     * @return void
     */
    public static function createSlots($eventId , $sponsorId){
        $eventInfo = \App\Models\Event::find($eventId);
        $setting = self::getSetting($eventId);
        if($setting->reservations_view == 1 && $setting->reservation_type == 1 &&  $setting->reservation == 1){
            if(!empty($setting->start_time) && !empty($setting->end_time) && !empty($setting->duration) && $setting->start_time != "00:00" && $setting->end_time != "00:00" && $setting->duration != "0"){
                $data = array();
                $mapping = array('date', 'time_from', 'time_to', 'duration', 'entity_id','sponsor_name');
                $period = \Carbon\CarbonPeriod::create($eventInfo->start_date, $eventInfo->end_date);
                if(count($period) < 7) {
                    foreach ($period as $date) {
                        $tempArray = array();
                        $tempArray[0] = $date;
                        $tempArray[1] = $setting->start_time;
                        $tempArray[2] = $setting->end_time;
                        $tempArray[3] = $setting->duration;
                        $tempArray[4] = $sponsorId;
                        $tempArray[5] = '';
                        $data[] = $tempArray;
                    }
                    self::importReservations($mapping, $data, '', $eventId);
                }
            }
        }
    }
    
    /**
     * importReservations
     *
     * @param  mixed $mapping
     * @param  mixed $data
     * @param  mixed $import_type
     * @param  mixed $eventId
     * @return void
     */
    static public function importReservations($mapping, $data, $import_type='', $eventId = '')
    {
        $result = array();
        $result['new'] = array();
        $result['duplicate'] = array();
        $result['error'] = array();
        $settings = self::getImportSettingsReservations();
        foreach ($data as $key => $values) {
            $db_data = array();
            foreach ($values as $index => $val) {
                $db_data[$mapping[$index]] = trim($val);
                $values['error'] = '';
                if ($settings['fields'][$mapping[$index]]['required'] == 1 && trim($val) == '') {
                    $values['error'] = 'Field: ' . $mapping[$index] . ' have invalid value: ' . $val;
                    $result['error'][] = $values;
                    $db_data = '';
                    unset($db_data);
                    break;
                }
            }
            $db_data['event_id'] = $eventId;
            $db_data['entity_type'] = 'S';
            $db_data['timeFrom'] = trim($db_data['time_from']);
            $db_data['timeTo'] = trim($db_data['time_to']);
            $db_data['date'] = date("Y-m-d",strtotime($db_data['date']));
            if(strtotime($db_data['start_time']) < strtotime($db_data['end_time'])){
                $values['error'] = 'Start time must be less then end time';
                $result['error'][] = $values;
                $db_data = '';
                unset($db_data);
                break;
            }
            if(EventRepository::checkEventDate($eventId, $db_data['date'])){
                $values['error'] = 'Reservation date (' . $db_data['date'] . ') must be between event dates';
                $result['error'][] = $values;
                $db_data = '';
                unset($db_data);
                break;
            }
            if ($values['error'] == '') {
                $result['new'][] = $values;
                $db_data['organizer_id'] = \Auth::user()->id;
                $check_reservation_exist = ReservationRepository::reservationExist($eventId, $db_data['date'],$db_data['timeFrom'],$db_data['timeTo'],$db_data['entity_id'],$db_data['entity_type']);
                if($check_reservation_exist > 0) {
                    $master_id = $check_reservation_exist;
                }
                else{
                    $new_create = \App\Models\Reservation::create($db_data);
                    $master_id = $new_create->id;
                }
                $persons = \App\Models\SponsorAttendee::select('attendee_id')->where('sponsor_id', '=', $db_data['entity_id'])->get()->toArray();
                foreach($persons as $assignCat) {
                    self::create_slots($db_data, $assignCat['attendee_id'], $master_id, $eventId);
                }
            }
        }

        return $result;
    }

    public static function createReservationSlots($db_data, $contact_id, $master_id, $eventId = '')
    {
        $db_data['event_id'] = $eventId;
        $slots = getTimeSlots($db_data['duration'], 0, date("H:i:s", strtotime($db_data['timeFrom'])), date("H:i:s", strtotime($db_data['timeTo'])));
        for ($i = 0; $i < count($slots); $i++) {
            $slot = explode('-', $slots[$i]);
            $db_data['master_id'] = $master_id;
            $db_data['time_from'] = $slot[0];
            $db_data['time_to'] = $slot[1];
            $db_data['contact_id'] = $contact_id;
            if (ReservationRepository::slotExist($db_data['date'], $db_data['time_from'], $db_data['time_to'], $db_data['entity_id'], $db_data['entity_type'], $contact_id, $db_data['event_id'])) {
                if((strtotime($slot[1]) - strtotime($slot[0]))/60 == $db_data['duration']) {
                    \App\Models\ReservationSlot::create($db_data);
                }
            }
        }
    }

}
