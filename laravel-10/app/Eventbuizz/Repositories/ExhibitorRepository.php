<?php

namespace App\Eventbuizz\Repositories;

use App\Models\EventExhibitor;
use App\Models\EventExhibitorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ExhibitorRepository extends AbstractRepository
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
	 *Delete Attendee Exhibtor Info
	 *
	 * @param int or @param array
	 * @param int
	 */
	public static function deleteExhibtorContact($attendee_id, $event_id)
	{
		$exhibitor_ids = \App\Models\EventExhibitor::where('event_id', '=', $event_id)->pluck('id')->toArray();
		if (is_array($attendee_id)) {
			\App\Models\EventExhibitorAttendee::whereIn('exhibitor_id', $exhibitor_ids)->whereIn('attendee_id', $attendee_id)->delete();
			\App\Models\ReservationSlot::where('event_id', '=', $event_id)->whereIn('contact_id', $attendee_id)->delete();
			\App\Models\ReservationSlot::where('event_id', '=', $event_id)->whereIn('reserved_by', $attendee_id)->delete();
			\App\Models\ReservationLog::where('event_id', '=', $event_id)->whereIn('contact_id', $attendee_id)->delete();
			\App\Models\ReservationLog::where('event_id', '=', $event_id)->whereIn('reserved_by', $attendee_id)->delete();
		} else {
			\App\Models\EventExhibitorAttendee::whereIn('exhibitor_id', $exhibitor_ids)->where('attendee_id', '=', $attendee_id)->delete();
			\App\Models\ReservationSlot::where('event_id', '=', $event_id)->where('contact_id', '=', $attendee_id)->delete();
			\App\Models\ReservationSlot::where('event_id', '=', $event_id)->where('reserved_by', '=', $attendee_id)->delete();
			\App\Models\ReservationLog::where('event_id', '=', $event_id)->where('contact_id', '=', $attendee_id)->delete();
			\App\Models\ReservationLog::where('event_id', '=', $event_id)->where('reserved_by', '=', $attendee_id)->delete();
		}
	}

	/**
	 *exhibitors
	 *
	 * @param array
	 */
	static public function getExhibitors($formInput)
	{
		$array = array();
		$results = \App\Models\EventExhibitor::where('event_id', $formInput['event_id'])->get();
		foreach ($results as $row) {
			$array[$row['id']] = $row->name;
		}
		return $array;
	}


    /**
     * @param $formInput
     * @return mixed
     */

     public function getEventSiteExhibitorsListing($formInput) {

        $event_id = $formInput['event_id'];

        $lang_id = $formInput['language_id'];

        $category_id = $formInput['category_id'];

		$query = $formInput['query'];

		if(isset($formInput['event']['exhibitor_settings']) && $formInput['event']['exhibitor_settings']) {
            $exhibitor_settings = $formInput['event']['exhibitor_settings'];
        } else {
            $exhibitor_settings = self::getSetting($event_id);
        }

        $sortType = $exhibitor_settings['sortType'];

        $exhibitors = EventExhibitor::with(['categories', 'attendeeExhibitors', 'categories.info' => function($q) use($lang_id) { $q->where('languages_id', $lang_id); }
        ])->where('event_id', $event_id);

        if($category_id > 0) {
            $exhibitors = $exhibitors->whereHas('categories',function ($query) use($category_id) {
                return $query->where('conf_event_exhibitor_categories.category_id', $category_id);
            });
        }

        if($query) {
            $exhibitors = $exhibitors->where('name', 'like', $query . '%');
        }

        $exhibitors = $exhibitors->orderBy('name', 'ASC')->get()->toArray();

		foreach ($exhibitors as $key => $value) {

            $value = $this->refactorExhibitorData($value, $exhibitor_settings);

            $exhibitors[$key] = $value;

            if(count($value['categories']) > 0) {
                $exhibitors[$key]['sort_order'] = $value['categories'][0]['sort_order'];
                foreach($value['categories'] as $i => $category) {
                    $exhibitors[$key]['categories'][$i]['info'] = readArrayKey($category, [], 'info');
                }
            }else{
                $exhibitors[$key]['sort_order'] = 5000;
			}

        }

		if($exhibitor_settings['sortType'] == 1){
            $sort = array();
            foreach($exhibitors as $k=>$v) {
                $sort['name'][$k] = $v['name'];
                $sort['sort_order'][$k] = $v['sort_order'];
            }
            array_multisort($sort['sort_order'], SORT_ASC,$sort['name'], SORT_ASC,SORT_NATURAL|SORT_FLAG_CASE, $exhibitors);
        }

		return $exhibitors;
    }
	
	/**
     * @param mixed $attendee
     * @param mixed $settings
     * 
     * @return [type]
     */
    public function refactorExhibitorData($exhibitor, $settings, $detail=false)
    {
		$exhibitor['email'] = $settings['exhibitorEmail'] === 1 ? $exhibitor['email'] : "";

		$exhibitor['phone_number'] = $settings['exhibitorPhone'] === 1 ? $exhibitor['phone_number'] : "";

		if(!$detail) {
			$exhibitor['name'] = $settings['exhibitorName'] === 1 ? $exhibitor['name'] : "";
		}

		$exhibitor['booth'] = $settings['show_booth'] === 1 ? $exhibitor['booth'] : "";

		$exhibitor['show_cat'] = $settings['catTab'] === 1 ? true : false;

		if($exhibitor['info']) {
            foreach ($exhibitor['info'] as $key => $value) {
                $exhibitor[$value['name']] = $value['value'];
            }
            unset($exhibitor['info']);
        }

		return $exhibitor;
    }


	public function getEventSiteExhibitors($formInput)
    {
		$event_id = $formInput['event_id'];
		$lang_id = $formInput['language_id'];

		$exhibitorCategories = \App\Models\EventCategory::where("event_id", $event_id)
		->where('cat_type', 'exhibitors')
		->where('parent_id', 0)
		->with(['info' => function ($q) use ($lang_id) {
			return $q->where('languages_id', $lang_id);
		}])->orderBy('sort_order')->get()->toArray();

		foreach ($exhibitorCategories as $key => $exhibitorCategory) {

			$categoryExhibitor = \App\Models\EventExhibitor::where("event_id", $event_id)
			->whereHas("categories", function ($q) use ($exhibitorCategory) {
				return $q->where('category_id', $exhibitorCategory['id']);
			})->with(['info' => function ($q) use ($lang_id) {
				return $q->where('languages_id', $lang_id);
			}])->get()->toArray();

			$exhibitorCategories[$key]['exhibitors'] = $categoryExhibitor;
		}
		$withoutCategories = \App\Models\EventExhibitor::where("event_id", $event_id)
		->whereDoesntHave("categories")
		->with(['info' => function ($q) use ($lang_id) {
			return $q->where('languages_id', $lang_id);
		 }])->get()->toArray();

		if (!empty($withoutCategories)) {
			$exhibitorCategories[]['exhibitors'] = $withoutCategories;
		}
		foreach ($exhibitorCategories as $key => $category) {
            foreach ($category['info'] as $info) {
                    $category[$info['name']] = $info['value'];
            }
            unset($category['info']);
            foreach ($category['exhibitors'] as $ikey => $exhibitor) {
                    foreach ($exhibitor['info'] as $info) {
                        $exhibitor[$info['name']] = $info['value'];
                    }
                    unset($exhibitor['info']);
                $category['exhibitors'][$ikey] = $exhibitor;
            }
            $exhibitorCategories[$key] = $category;
        }
        return $exhibitorCategories;
    }


	public function getExhibitorCategories($formInput)
    {
        $exhibitorCategories = \App\Models\EventCategory::where("event_id", $formInput['event_id'])->where('cat_type', 'exhibitors')->with(['info'])->orderBy('sort_order')->get()->toArray();
		foreach ($exhibitorCategories as $key => $category) {
			foreach ($category['info'] as  $info) {
				$category[$info['name']] = $info['value'];
			}
			unset($category['info']);
			$exhibitorCategories[$key] = $category;
		}
		return $exhibitorCategories;
    }


	public function getEventSiteExhibitorDetail($formInput, $exhibitor_id)
	{
		$event_id = $formInput['event_id'];

        $lang_id = $formInput['language_id'];

        $sponsor_settings = $formInput['event']['sponsor_settings'];

        $speaker_settings = $formInput['event']['speaker_settings'];

        $exhibitor_settings = $formInput['event']['exhibitor_settings'];

        $attendee_settings = $formInput['event']['attendee_settings'];

        $exhibitor = \App\Models\EventExhibitor::where('id', $exhibitor_id)->with([
            'info' => function ($q) use ($lang_id) {
                $q->where('languages_id', $lang_id);
            },
        ]);

		if($exhibitor_settings['show_contact_person']) {

			$exhibitor = $exhibitor->with([
				'exhibitorsAttendee.attendees' => function ($query) {
					return $query->select('id', 'first_name', 'last_name', 'image', 'email');
				}, 'exhibitorsAttendee.attendees.info' => function ($query) use($lang_id) {
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
				}, 'exhibitorsAttendee.attendees.currentEventAttendee',]);

		}

		$exhibitor = $exhibitor->where('event_id', $event_id);

        $exhibitor = $exhibitor->first();

        $exhibitor = $exhibitor ? $exhibitor->toArray() : null;

        if($exhibitor !== null) {

            $exhibitor = $this->refactorExhibitorData($exhibitor, $exhibitor_settings, true);

			if ($exhibitor_settings['show_contact_person']) {

				$temp_array = [];

				foreach ($exhibitor['exhibitors_attendee'] as $key => $contact_person) {
					
					$gdpr = $formInput['event']['gdpr_settings']['enable_gdpr'] ? $contact_person['attendees']['current_event_attendee']['gdpr'] : 1;

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
							"first_name" => $contact_person['last_name'],
							"last_name" => $contact_person['last_name'],
						];

					}

				}

				$exhibitor['exhibitors_attendee'] = $temp_array;
			}
        }
         
        return $exhibitor;
	}

	public function getExhibitorDocument($formInput, $exhibitor_id)
    {
        $event_id = $formInput['event_id'];
        $lang_id = $formInput['language_id'];

        $parentDirectory = \App\Models\Directory::where('event_id', $event_id)->where('exhibitor_id', $exhibitor_id)->first();
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

        $directories = \App\Models\Directory::where('event_id', '=', $event_id)->where('parent_id', $id)->with(['info' => function ($query) use ($lang_id) {
            return $query->where('languages_id', $lang_id);
        }])->with(['files', 'files.info' => function ($query) use ($lang_id) {
            return $query->where('languages_id', $lang_id);
        }])->orderby('sort_order', 'ASC')->get()->toArray();

        foreach ($directories as $key => $dir) {
            foreach ($dir['info'] as $item) {
                $dir[$item['name']] = $item['value'];
            }
            unset($dir['info']);
            foreach ($dir['files'] as $ikey => $file) {
                foreach ($file['info'] as $info) {
                    $file[$info['name']] = $info['value'];
                }
                unset($file['info']);

				if ($file['s3'] == 1) {
					$file['s3_url'] = getS3Image('assets/directory/' . $file['path']);
				}
				
                $dir['files'][$ikey] = $file;
            }
            $directories[$key]['children'] = $this->getBreadcrumb($dir['id'], $event_id, $lang_id);
            $new_array = array_values(Arr::sort(array_merge($directories[$key]['children'], $dir['files']), function ($value) {
                return $value['sort_order'];
            }));

            $directories[$key] = $dir;
            $directories[$key]['children_files'] = $new_array;
        }
        return $directories;
    }
	
	/**
     * getSetting
     *
     * @param  mixed $event_id
     * @return void
     */
    public static function getSetting($event_id) {
        return \App\Models\ExhibitorSetting::where('event_id',$event_id)->get()->first();
    }

	/**
	 * createSlots
	 *
	 * @param  mixed $eventId
	 * @param  mixed $sponsorId
	 * @return void
	 */
	public static function createSlots($eventId , $sponsorId){
		$eventInfo = \App\Models\Event::find($eventId);
		$setting = self::getSetting($eventId);
		if($setting->reservations_view == 1 && $setting->reservation_type == 1 && $setting->reservation == 1){
			if(!empty($setting->start_time) && !empty($setting->end_time) && !empty($setting->duration) && $setting->start_time != "00:00" && $setting->end_time != "00:00" && $setting->duration != "0"){
				$data =array();
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
					self::importReservations($mapping, $data, '', $eventId, $eventInfo->organizer_id);
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
	public static function importReservations($mapping, $data, $import_type='', $eventId = '', $organizer_id = '')
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

			//Check Reservations slots are in between event dates
			if(EventRepository::checkEventDate($eventId, $db_data['date'])){
				$values['error'] = 'Reservation date (' . $db_data['date'] . ') must be between event dates';
				$result['error'][] = $values;
				$db_data = '';
				unset($db_data);
				break;
			}

			if ($values['error'] == '') {
				$result['new'][] = $values;
				$db_data['organizer_id'] = $organizer_id;
				$check_reservation_exist = ReservationRepository::reservationExist($eventId, $db_data['date'],$db_data['timeFrom'],$db_data['timeTo'],$db_data['entity_id'],$db_data['entity_type']);
				if($check_reservation_exist > 0) {
					$master_id = $check_reservation_exist;
				} else{
					$new_create = \App\Models\Reservation::create($db_data);
					$master_id = $new_create->id;
				}
				$persons = \App\Models\SponsorAttendee::select('attendee_id')->where('sponsor_id', '=', $db_data['entity_id'])->get()->toArray();
				foreach($persons as $assignCat) {
					self::createReservationSlots($db_data, $assignCat['attendee_id'], $master_id, $eventId);
				}
			}
		}
        return $result;
    }
	
	/**
	 * createReservationSlots
	 *
	 * @param  mixed $db_data
	 * @param  mixed $contact_id
	 * @param  mixed $master_id
	 * @param  mixed $eventId
	 * @return void
	 */
	public function createReservationSlots($db_data, $contact_id, $master_id, $eventId = '')
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